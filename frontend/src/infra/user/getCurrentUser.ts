import { User } from '@/domain/User'
import { ApiClient } from '../lib/apiClient'

export const makeGetCurrentUser = (apiClient: ApiClient) => async (): Promise<User> => {
  const response = await apiClient('/v1/users/me')

  if (!response.ok) {
    throw new Error('Failed to fetch Current User')
  }

  return await response.json()
}
