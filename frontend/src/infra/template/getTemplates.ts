import { Template } from '@/domain/Template'
import { ApiClient } from '../lib/apiClient'

export const getTemplates = async (
  apiClient: ApiClient,
  { userId }: { userId: number }
): Promise<Array<Template>> => {
  const params = new URLSearchParams({ user_id: userId.toString() })
  const response = await apiClient(`/v1/timelog/templates?${params}`)

  if (!response.ok) {
    throw new Error('Failed to fetch Templates')
  }

  return await response.json()
}
