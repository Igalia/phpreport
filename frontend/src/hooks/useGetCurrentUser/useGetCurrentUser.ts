import { useSession } from 'next-auth/react'
import { Session } from 'next-auth'

export const useGetCurrentUser = () => {
  const { data } = useSession({
    required: true
  })

  if (!data) {
    return {} as Session['user']
  }

  return data.user
}
