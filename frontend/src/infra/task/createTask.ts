import { Task } from '@/domain/Task'
import { TaskIntent } from '@/domain/Task'
import { ApiClient } from '../lib/apiClient'

export const createTask = async (task: TaskIntent, apiClient: ApiClient): Promise<Task | Error> => {
  return apiClient('/v1/timelog/tasks', {
    method: 'POST',
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
    .then((response) => response.json())
    .catch((e) => {
      return new Error(e)
    })
}
