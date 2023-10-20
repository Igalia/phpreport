import { validateToken } from './validateToken'
import { RequestInit } from 'next/dist/server/web/spec-extension/request'

export type ApiClient = (url: string, config?: RequestInit) => Promise<Response>

type FetchFactory = ({ baseURL, token }: { baseURL: string; token?: string }) => ApiClient

export const fetchFactory: FetchFactory = ({ baseURL, token }) => {
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
