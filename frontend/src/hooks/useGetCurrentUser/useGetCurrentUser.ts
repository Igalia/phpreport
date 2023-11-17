import { useSession } from 'next-auth/react'

export const useGetCurrentUser = () => {
  const { data } = useSession({
    required: true
  })

  return data!.user
}
