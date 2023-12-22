import { useMutation, useQueryClient } from '@tanstack/react-query'
import { TaskIntent, Task } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'
import { editTask } from '@/infra/task/editTask'

export const useEditTask = ({ handleSuccess }: { handleSuccess: () => void }) => {
  const apiClient = useClientFetch()
  const { showError, showSuccess } = useAlert()
  const { id: userId } = useGetCurrentUser()
  const queryClient = useQueryClient()

  const { mutate } = useMutation((task: TaskIntent) => editTask(task, apiClient), {
    onSuccess: (data) => {
      queryClient.setQueryData<Array<Task>>(['tasks', userId], (prevData) =>
        prevData!.map((task) => {
          if (task.id === data.id) {
            return data
          }

          return task
        })
      )
      showSuccess('Task succesfully edited')
      handleSuccess()
    },
    onError: () => {
      showError('Failed to edit task')
    }
  })

  return { editTask: mutate }
}
