import { ApiClient } from '@/infra/lib/apiClient'
import { Task } from '@/domain/Task'

type GetTasksProps = Pick<Task, 'userId' | 'startTime' | 'endTime'>

export const makeGetTasks =
  (apiClient: ApiClient) =>
  async ({ userId, startTime, endTime }: GetTasksProps): Promise<Array<Task>> => {
    const params = new URLSearchParams({
      user_id: userId.toString(),
      start: startTime,
      end: endTime
    })
    const response = await apiClient(`/v1/timelog/tasks?${params}`, { next: { tags: ['tasks'] } })

    if (!response.ok) {
      throw new Error('Failed to fetch Tasks')
    }

    return response.json()
  }
