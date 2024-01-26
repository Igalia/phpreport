import { ApiClient } from '../lib/apiClient'
import { Task } from '@/domain/Task'

export const makeDeleteTask =
  (apiClient: ApiClient) =>
  async (taskId: Task['id']): Promise<string> => {
    try {
      const response = await apiClient(`/v1/timelog/tasks/${taskId}`, {
        method: 'DELETE'
      })

      if (!response.ok) {
        throw Error(response.statusText)
      }

      return response.statusText
    } catch (e) {
      throw e
    }
  }
