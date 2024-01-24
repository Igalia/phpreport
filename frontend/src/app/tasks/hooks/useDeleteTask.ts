import { useMutation, useQueryClient } from '@tanstack/react-query'
import { Task } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { makeDeleteTask } from '@/infra/task/deleteTask'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'

export const useDeleteTask = () => {
  const apiClient = useClientFetch()
  const queryClient = useQueryClient()
  const { id: userId } = useGetCurrentUser()
  const { showError, showSuccess } = useAlert()
  const deleteTask = makeDeleteTask(apiClient)

  const { mutate } = useMutation((taskId: number) => deleteTask(taskId), {
    onSuccess: (_, taskId) => {
      queryClient.setQueryData<Array<Task>>(['tasks', userId], (prevData) =>
        prevData!.filter((prevData) => prevData.id !== taskId)
      )
      queryClient.invalidateQueries(['workSummary', userId])
      showSuccess('Task successfully removed')
    },
    onError: () => {
      showError('Failed to remove task')
    }
  })

  return { deleteTask: mutate }
}
