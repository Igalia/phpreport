import { getServerSession } from 'next-auth'
import { authOptions } from '@/app/api/auth/[...nextauth]/route'
import { validateToken } from './validateToken'
import { RequestInit } from 'next/dist/server/web/spec-extension/request'

export const apiClient = async (url: string, config: RequestInit = {}) => {
  const session = await getServerSession(authOptions)

  const isAccessTokenValid = session?.accessToken && validateToken(session.accessToken)

  if (isAccessTokenValid) {
    config.headers = {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${session.accessToken}`
    }
  }

  return fetch(`${process.env.PUBLIC_API_BASE}${url}`, config)
}
