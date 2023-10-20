import { useSession } from 'next-auth/react'
import { useMemo } from 'react'
import { fetchFactory } from './apiClient'

export const useClientFetch = () => {
  const { data: session } = useSession()

  const apiClient = useMemo(
    () => fetchFactory({ baseURL: process.env.NEXT_PUBLIC_API_BASE!, token: session?.accessToken }),
    [session?.accessToken]
  )

  return apiClient
}
