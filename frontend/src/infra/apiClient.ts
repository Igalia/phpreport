//import config from './config';
import jwtDecode from 'jwt-decode'
import axios from 'axios'

type DecodedAcessToken = {
  exp: number
}

export const apiClient = (token: string) => {
  const decodedAccessToken: DecodedAcessToken = jwtDecode(token)
  const isAccessTokenValid = decodedAccessToken.exp * 1000 > new Date().getTime()

  const initialConfig = {
    baseURL: process.env.NEXT_PUBLIC_API_BASE
  }
  const client = axios.create(initialConfig)
  if (isAccessTokenValid) {
    console.log('token is valid')
    client.interceptors.request.use((config) => {
      config.headers['Authorization'] = `Bearer ${token}`
      return config
    })
  } else {
    console.log('your login session is not valid')
    //TODO: do something more useful here
  }
  return client
}
