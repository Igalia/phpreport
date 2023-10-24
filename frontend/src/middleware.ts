import { getToken } from 'next-auth/jwt'
import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'
import { validateToken } from '@/infra/lib/validateToken'

const allowedRoutes = ['/_next', '/api/auth']

// This function can be marked `async` if using `await` inside
export async function middleware(request: NextRequest) {
  // Get the pathname of the request (e.g. /, /protected)
  const path = request.nextUrl.pathname

  const isAllowed = allowedRoutes.some((route) => path.includes(route))

  const token = await getToken({
    req: request,
    secret: process.env.NEXTAUTH_SECRET
  })

  const isAccessTokenValid = token?.accessToken && validateToken(token.accessToken)

  if (!isAccessTokenValid && !isAllowed) {
    return NextResponse.redirect(new URL('/web/v2/api/auth/signin', request.url))
  }
  if(isAccessTokenValid){
    if(!token.user?.id){
      return new NextResponse("You do not have a user record in the application. Please contact your sysadmin.", {status: 401})
    }
    if(!token.user.roles || token.user.roles.length ==0){
      return new NextResponse("You have not been assigned any roles in the application. Please contact your sysadmin.", {status: 403})
    }
    if(path == "/tasks" && !token.user.authorizedScopes.includes("task:read-own")){
      return NextResponse.redirect(new URL('/web/v2/auth/error?error=AccessDenied', request.url))
    }
  }

  return NextResponse.next()
}
