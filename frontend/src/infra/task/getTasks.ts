import { ApiClient } from '@/infra/lib/apiClient'
import { Task } from '@/domain/Task'

export const getTasks = async (
  apiClient: ApiClient,
  { userId, start, end }: { userId: number; start: string; end: string }
): Promise<Array<Task>> => {
  const params = new URLSearchParams({ user_id: userId.toString(), start, end })
  const response = await apiClient(`/v1/timelog/tasks?${params}`)

  if (!response.ok) {
    throw new Error('Failed to fetch Tasks')
  }

  return await response.json()
}
