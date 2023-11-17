import { User } from '@/domain/User'
import { DefaultSession } from 'next-auth'

declare module 'next-auth' {
  /**
   * Returned by `useSession`, `getSession` and received as a prop on the `SessionProvider` React Context
   */
  interface Session {
    accessToken?: string
    user: User & DefaultSession['user']
    accessTokenExpires?: number
    refreshToken?: string
  }

  /**
   * Usually contains information about the provider being used
   * and also extends `TokenSet`, which is different tokens returned by OAuth Providers.
   */
  interface Account {
    access_token: string
    expires_at: number
  }

  /** The OAuth profile returned from your provider */
  interface Profile {
    id: string
  }
}

declare module 'next-auth/jwt' {
  /** Returned by the `jwt` callback and `getToken`, when using JWT sessions */
  interface JWT {
    id?: string
    accessToken?: string
    user?: User
    accessTokenExpires?: number
    refreshToken?: string
  }
}
