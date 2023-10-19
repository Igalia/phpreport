import { authOptions } from '@/app/api/auth/[...nextauth]/route'
import { getServerSession } from 'next-auth'
import { fetchFactory } from './apiClient'

export const serverFetch = async () => {
  const session = await getServerSession(authOptions)

  return fetchFactory({ baseURL: process.env.API_BASE!, token: session?.accessToken })
}
