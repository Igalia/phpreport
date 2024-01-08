import { useMutation, useQueryClient } from '@tanstack/react-query'
import { TaskIntent } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { makeCreateTask } from '@/infra/task/createTask'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'
import { useGetTasks } from './useGetTasks'
import { BaseError } from '@/_lib/errors/BaseError'

export const useCreateTask = () => {
  const apiClient = useClientFetch()
  const createTask = makeCreateTask(apiClient)
  const queryClient = useQueryClient()
  const { showError, showSuccess } = useAlert()
  const { id: userId } = useGetCurrentUser()
  const { tasks } = useGetTasks()

  const { mutate } = useMutation((task: TaskIntent) => createTask(task, tasks), {
    onSuccess: () => {
      queryClient.invalidateQueries(['tasks', userId])
      showSuccess('Task added succesfully')
    },
    onError: (e) => {
      if (e instanceof BaseError) {
        showError(e.message)
        return
      }

      throw e
    }
  })

  return { addTask: mutate }
}
