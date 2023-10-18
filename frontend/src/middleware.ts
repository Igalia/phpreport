import { getToken } from 'next-auth/jwt'
import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'
import { validateToken } from './infra/lib/validateToken'

const protectedRoutes = ['/tasks']

// This function can be marked `async` if using `await` inside
export async function middleware(request: NextRequest) {
  // Get the pathname of the request (e.g. /, /protected)
  const path = request.nextUrl.pathname

  const isProtected = protectedRoutes.some((route) => path.includes(route))

  const session = await getToken({
    req: request,
    secret: process.env.NEXTAUTH_SECRET
  })

  const isAccessTokenValid = session?.accessToken && validateToken(session.accessToken)

  if (!isAccessTokenValid && isProtected) {
    return NextResponse.redirect(new URL('/web/v2/auth/signin', request.url))
  }

  return NextResponse.next()
}
