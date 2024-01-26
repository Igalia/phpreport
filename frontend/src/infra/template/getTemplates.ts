import { Template } from '@/domain/Template'
import { ApiClient } from '../lib/apiClient'
import { User } from '@/domain/User'

export const makeGetTemplates =
  (apiClient: ApiClient) =>
  async ({ userId }: { userId: User['id'] }): Promise<Array<Template>> => {
    const params = new URLSearchParams({ user_id: userId.toString() })
    const response = await apiClient(`/v1/timelog/templates?${params}`, {
      next: { tags: ['templates'] }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch Templates')
    }

    return await response.json()
  }
