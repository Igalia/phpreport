import { User } from '@/domain/User'
import { ApiClient } from '../lib/apiClient'
import { getServerSession } from 'next-auth'
import { authOptions } from '@/app/api/auth/[...nextauth]/route'

export const getCurrentUser = async (apiClient: ApiClient): Promise<User> => {
  const [session, response] = await Promise.all([
    getServerSession(authOptions),
    apiClient('/v1/users/me')
  ])

  if (!session) {
    throw new Error('Failed to fetch Session')
  }

  if (!response.ok) {
    throw new Error('Failed to fetch Current User')
  }

  const user = await response.json()

  return {
    ...session.user,
    ...user
  }
}
