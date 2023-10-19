import { useMutation } from '@tanstack/react-query'
import { TaskIntent } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { createTask } from '@/infra/task/createTask'
import { useClientFetch } from '@/infra/lib/useClientFetch'

export const useAddTask = () => {
  const apiClient = useClientFetch()
  const { showError, showSuccess } = useAlert()

  const { mutate } = useMutation((task: TaskIntent) => createTask(task, apiClient), {
    onSuccess: () => {
      showSuccess('Task added succesfully')
    },
    onError: () => {
      showError('Failed to add task')
    }
  })

  return { addTask: mutate }
}
