import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { TaskIntent } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { createTask } from '@/infra/task/createTask'
import { getTasks } from '@/infra/task/getTasks'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { format } from 'date-fns'
import { Task } from '@/domain/Task'

export const useGetTasks = (userId: number) => {
  const apiClient = useClientFetch()
  const today = format(new Date(), 'yyyy-MM-dd')

  const { data } = useQuery({
    queryKey: ['tasks', userId],
    queryFn: () => getTasks(apiClient, { userId, start: today, end: today }),
    initialData: []
  })

  return data
}

export const useAddTask = (userId: number) => {
  const apiClient = useClientFetch()
  const queryClient = useQueryClient()
  const { showError, showSuccess } = useAlert()

  const { mutate } = useMutation((task: TaskIntent) => createTask(task, apiClient), {
    onSuccess: (data) => {
      queryClient.setQueryData<Array<Task>>(['tasks', userId], (oldData) => {
        if (oldData) {
          return [...oldData, data]
        }
        return [data]
      })
      showSuccess('Task added succesfully')
    },
    onError: () => {
      showError('Failed to add task')
    }
  })

  return { addTask: mutate }
}
