import { useAuth } from 'react-oidc-context'
import { apiClient } from '@/infra/apiClient'
import { useMutation } from '@tanstack/react-query'
import { TaskIntent, Task } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'

const addTask = ({ token, task }: { token: string; task: TaskIntent }): Promise<Task> => {
  return apiClient(token)
    .post('/v1/timelog/tasks', {
      project_id: task.projectId,
      story: task.story,
      description: task.description,
      task_type: task.taskType,
      start_time: task.startTime,
      end_time: task.endTime,
      user_id: task.userId,
      date: task.date
    })
    .then((response) => response.data)
}

export const useAddTask = () => {
  const auth = useAuth()
  const { showError, showSuccess } = useAlert()
  const token = auth.user?.access_token || ''

  const { mutate } = useMutation((task: TaskIntent) => addTask({ token, task }), {
    onSuccess: () => {
      showSuccess('Task added succesfully')
    },
    onError: () => {
      showError('Failed to add task')
    }
  })

  return { addTask: mutate }
}
