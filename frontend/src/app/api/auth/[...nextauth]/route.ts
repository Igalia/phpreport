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
  callbacks: {
    async redirect({ baseUrl }) {
      return `${baseUrl}/web/v2/`
    },
    async session({ session, token }) {
      session.accessToken = token.accessToken
      session.user.id = token.id

      return session
    },
    async jwt({ token, account, profile }) {
      if (account && profile) {
        token.accessToken = account.access_token
        token.id = profile.id
      }
      return token
    }
  }
}

const handler = NextAuth(authOptions)

export { handler as GET, handler as POST }
