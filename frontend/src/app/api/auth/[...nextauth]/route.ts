import NextAuth, { NextAuthOptions } from 'next-auth'
import KeycloakProvider from 'next-auth/providers/keycloak'
import { fetchFactory } from '@/infra/lib/apiClient'
import { makeGetCurrentUser } from '@/infra/user/getCurrentUser'
import { JWT } from 'next-auth/jwt'

/**
 * Takes a token, and returns a new token with updated
 * `accessToken` and `accessTokenExpires`. If an error occurs,
 * returns the old token and an error property
 */
async function refreshAccessToken(token: JWT) {
  try {
    const url = `${process.env.OIDC_TOKEN_ENDPOINT}`

    const params = {
      grant_type: 'refresh_token',
      client_id: process.env.OIDC_CLIENT_ID!,
      client_secret: process.env.OIDC_CLIENT_SECRET!,
      refresh_token: token.refreshToken!
    }

    const response = await fetch(url, {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams(params),
      method: 'POST'
    })

    const refreshedTokens = await response.json()

    if (!response.ok) {
      throw refreshedTokens
    }

    return {
      ...token,
      accessToken: refreshedTokens.access_token,
      accessTokenExpires: Date.now() + refreshedTokens.expires_in * 1000,
      refreshToken: refreshedTokens.refresh_token ?? token.refreshToken // Fall back to old refresh token
    }
  } catch (error) {
    return {
      ...token,
      error: 'RefreshAccessTokenError'
    }
  }
}

export const authOptions: NextAuthOptions = {
  providers: [
    KeycloakProvider({
      clientId: process.env.OIDC_CLIENT_ID!,
      clientSecret: process.env.OIDC_CLIENT_SECRET!,
      issuer: process.env.OIDC_AUTHORITY
    })
  ],
  pages: {
    error: '/auth/error'
  },
  callbacks: {
    async redirect({ baseUrl }) {
      return baseUrl
    },
    async session({ session, token }) {
      session.accessToken = token.accessToken
      session.user = { ...session.user, ...token.user }
      session.accessTokenExpires = token.accessTokenExpires
      session.refreshToken = token.refreshToken

      return session
    },
    async jwt({ token, account, profile, trigger }) {
      if (trigger === 'update' && Date.now() > token.accessTokenExpires!) {
        const newToken = await refreshAccessToken(token)

        return newToken
      }

      if (account && profile) {
        token.accessToken = account.access_token
        token.accessTokenExpires = account.expires_at * 1000
        token.refreshToken = account.refresh_token
        token.id = profile.id

        const apiClient = fetchFactory({ baseURL: process.env.API_BASE!, token: token.accessToken })

        const getCurrentUser = makeGetCurrentUser(apiClient)
        const user = await getCurrentUser()

        token.user = user
      }

      return token
    }
  }
}

const handler = NextAuth(authOptions)

export { handler as GET, handler as POST }
