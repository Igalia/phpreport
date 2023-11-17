import { User } from '@/domain/User'
import { ApiClient } from '../lib/apiClient'

export const getCurrentUser = async (apiClient: ApiClient): Promise<User> => {
  const response = await apiClient('/v1/users/me')

  if (!response.ok) {
    throw new Error('Failed to fetch Current User')
  }

  return await response.json()
}
