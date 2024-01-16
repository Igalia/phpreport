import { format } from 'date-fns'
import { ApiClient } from '@/infra/lib/apiClient'

export const makeGetProjectStats =
  (apiClient: ApiClient) =>
  async (projectId: string): Promise<any> => {
    const today = new Date()
    // TODO start getting the date range dynamically
    const firstDayOfYear = new Date(today.getFullYear(), 0, 1)
    const lastDayOfYear = new Date(today.getFullYear(), 11, 31)

    const params = new URLSearchParams({
      start: format(firstDayOfYear, 'yyyy-MM-dd'),
      end: format(lastDayOfYear, 'yyyy-MM-dd')
    })
    const response = await apiClient(`/v1/projects/${projectId}/stats?${params}`)

    if (!response.ok) {
      throw new Error('Failed to fetch Project Statistics')
    }

    return await response.json()
  }
