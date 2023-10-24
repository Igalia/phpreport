import NextAuth, { NextAuthOptions } from 'next-auth'
import KeycloakProvider from 'next-auth/providers/keycloak'


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
      return session
    },
    async jwt({ token, account, profile }) {
      if (account && profile) {
        token.accessToken = account.access_token
        token.id = profile.id

        const headers = {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token.accessToken}`
        }
        const response = await fetch(`${process.env.API_BASE}/v1/users/me`, { headers: { ...headers } })
        const result = await response.json()

        token.user = result
      }
      return token
    }
  }
}

const handler = NextAuth(authOptions)

export { handler as GET, handler as POST }
