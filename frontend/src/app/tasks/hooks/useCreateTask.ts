import { useMutation, useQueryClient } from '@tanstack/react-query'
import { TaskIntent } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { createTask } from '@/infra/task/createTask'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'

export const useCreateTask = () => {
  const apiClient = useClientFetch()
  const queryClient = useQueryClient()
  const { showError, showSuccess } = useAlert()
  const { id: userId } = useGetCurrentUser()

  const { mutate } = useMutation((task: TaskIntent) => createTask(task, apiClient), {
    onSuccess: () => {
      queryClient.invalidateQueries(['tasks', userId])
      showSuccess('Task added succesfully')
    },
    onError: () => {
      showError('Failed to add task')
    }
  })

  return { addTask: mutate }
}
