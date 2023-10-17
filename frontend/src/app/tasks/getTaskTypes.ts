import { apiClient } from '@/infra/apiClient'
import { TaskType } from '@/domain/TaskType'

export const getTaskTypes = async (): Promise<Array<TaskType>> => {
  const response = await apiClient('/v1/timelog/task_types/')

  if (!response.ok) {
    throw new Error('Failed to fetch TaskTypes')
  }

  return response.json()
}
