import { ApiClient } from '@/infra/lib/apiClient'
import { WorkSummary } from '@/domain/WorkSummary';

export const makeGetWorkSummary = (apiClient: ApiClient) => async (): Promise<WorkSummary> => {
  const response = await apiClient(`/v1/timelog/summary`)

  if (!response.ok) {
    throw new Error('Failed to fetch Work Summary')
  }

  return await response.json()
}
