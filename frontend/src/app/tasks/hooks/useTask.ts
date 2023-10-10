import { useAuth } from 'react-oidc-context'
import { apiClient } from '@/infra/apiClient'
import { useMutation } from '@tanstack/react-query'

type Task = {
  projectId: string
  taskType: string
  story: string
  description: string
  startTime: string
  endTime: string
  date: string
}

const addTask = ({ token, task }: { token: string; task: Task }): Promise<Task> => {
  return apiClient(token)
    .post('/v1/timelog/tasks', {
      project_id: task.projectId,
      story: task.story,
      description: task.description,
      start_time: task.startTime,
      end_time: task.endTime,
      date: task.date
    })
    .then((response) => response.data)
}

export const useAddTask = () => {
  const auth = useAuth()
  const token = auth.user?.access_token || ''

  const { mutate } = useMutation((task: Task) => addTask({ token, task }))

  return { addTask: mutate }
}
