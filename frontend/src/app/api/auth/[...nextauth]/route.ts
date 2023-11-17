import NextAuth, { NextAuthOptions } from 'next-auth'
import KeycloakProvider from 'next-auth/providers/keycloak'
import { fetchFactory } from '@/infra/lib/apiClient'
import { getCurrentUser } from '@/infra/user/getCurrentUser'

export const authOptions: NextAuthOptions = {
  providers: [
    KeycloakProvider({
      clientId: process.env.OIDC_CLIENT_ID!,
      clientSecret: process.env.OIDC_CLIENT_SECRET!,
      issuer: process.env.OIDC_AUTHORITY
    })
  ],
  pages: {
    error: '/web/v2/auth/error'
  },
  callbacks: {
    async redirect({ baseUrl }) {
      return `${baseUrl}/web/v2/`
    },
    async session({ session, token }) {
      session.accessToken = token.accessToken
      session.user = { ...session.user, ...token.user }
      return session
    },
    async jwt({ token, account, profile }) {
      if (account && profile) {
        token.accessToken = account.access_token
        token.id = profile.id

        const apiClient = fetchFactory({ baseURL: process.env.API_BASE!, token: token.accessToken })

        const user = await getCurrentUser(apiClient)

        token.user = user
      }
      return token
    }
  }
}

const handler = NextAuth(authOptions)

export { handler as GET, handler as POST }
