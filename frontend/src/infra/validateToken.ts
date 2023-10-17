import jwtDecode from 'jwt-decode'

type DecodedAcessToken = {
  exp: number
}

export const validateToken = (token: string) => {
  const decodedAccessToken: DecodedAcessToken = jwtDecode(token)
  return decodedAccessToken.exp * 1000 > new Date().getTime()
}
