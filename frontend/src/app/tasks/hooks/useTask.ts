import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { TaskIntent, Task } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { createTask } from '@/infra/task/createTask'
import { getTasks } from '@/infra/task/getTasks'
import { deleteTask } from '@/infra/task/deleteTask'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { format } from 'date-fns'

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

export const useDeleteTask = (userId: number) => {
  const apiClient = useClientFetch()
  const queryClient = useQueryClient()
  const { showError, showSuccess } = useAlert()

  const { mutate } = useMutation((taskId: number) => deleteTask(taskId, apiClient), {
    onSuccess: (_, taskId) => {
      queryClient.setQueryData<Array<Task>>(['tasks', userId], (prevData) =>
        prevData!.filter((prevData) => prevData.id !== taskId)
      )
      showSuccess('Task succesfully removed')
    },
    onError: () => {
      showError('Failed to remove task')
    }
  })

  return { deleteTask: mutate }
}
