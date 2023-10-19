import { useMemo } from 'react'
import { getServerSession } from 'next-auth'
import { validateToken } from './validateToken'
import { RequestInit } from 'next/dist/server/web/spec-extension/request'
import { authOptions } from '@/app/api/auth/[...nextauth]/route'
import { useSession } from 'next-auth/react'

export type ApiClient = (url: string, config?: RequestInit) => Promise<Response>

type FetchFactory = ({ baseURL, token }: { baseURL: string; token?: string }) => ApiClient

const fetchFactory: FetchFactory = ({ baseURL, token }) => {
  let headers = {}
  const isAccessTokenValid = token && validateToken(token)

  if (isAccessTokenValid) {
    headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`
    }
  }

  return (url: string, config: RequestInit = {}) =>
    fetch(`${baseURL}${url}`, { ...config, headers: { ...headers, ...config.headers } })
}

export const serverFetch = async () => {
  const session = await getServerSession(authOptions)

  return fetchFactory({ baseURL: process.env.API_BASE!, token: session?.accessToken })
}

export const useClientFetch = () => {
  const { data: session } = useSession()

  const apiClient = useMemo(
    () => fetchFactory({ baseURL: process.env.NEXT_PUBLIC_API_BASE!, token: session?.accessToken }),
    [session?.accessToken]
  )

  return apiClient
}
