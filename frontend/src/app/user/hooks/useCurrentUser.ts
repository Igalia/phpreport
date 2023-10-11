import { useQuery } from '@tanstack/react-query'
import { useAuth } from 'react-oidc-context'

import { apiClient } from '@/infra/apiClient'

type Roles = Array<string>

type User = {
  id: number
  username: string
  email: string
  firstName: string
  lastName: string
  roles: Roles
}

const fetchCurrentUser = (token: string): Promise<User> => {
  return apiClient(token)
    .get('/v1/users/users/me')
    .then((response) => response.data)
}

export const useCurrentUser = () => {
  const auth = useAuth()
  const token = auth.user?.access_token || ''

  const { data = {} as User, isLoading } = useQuery({
    queryKey: ['user', token],
    queryFn: () => {
      return fetchCurrentUser(token)
    }
  })

  return { user: data, isLoading }
}
