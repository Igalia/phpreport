import { Task, TaskIntent } from '@/domain/Task'
import { ApiClient } from '../lib/apiClient'

export const editTask = async (task: TaskIntent, apiClient: ApiClient): Promise<Task> => {
  return apiClient(`/v1/timelog/tasks/${task.id}`, {
    method: 'PUT',
    body: JSON.stringify({
      user_id: task.userId,
      project_id: task.projectId,
      story: task.story,
      description: task.description,
      task_type: task.taskType,
      start_time: task.startTime,
      end_time: task.endTime,
      date: task.date
    })
  })
    .then((response) => {
      if (!response.ok) {
        throw Error(response.statusText)
      }
      return response
    })
    .then((response) => response.json() as Promise<Task>)
    .catch((e) => {
      throw new Error(e)
    })
}
