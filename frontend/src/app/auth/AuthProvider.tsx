'use client'

import { AuthProvider as AuthProviderLib } from 'react-oidc-context'
import type { AuthProviderProps } from 'react-oidc-context'

const oidcConfig: AuthProviderProps = {
  authority: process.env.NEXT_PUBLIC_OIDC_AUTHORITY || '',
  client_id: process.env.NEXT_PUBLIC_OIDC_CLIENT_ID || '',
  client_secret: process.env.NEXT_PUBLIC_OIDC_CLIENT_SECRET,
  redirect_uri: process.env.NEXT_PUBLIC_OIDC_REDIRECT_URL || '',
  metadataUrl: process.env.NEXT_PUBLIC_OIDC_METADATA_URL,
  response_type: process.env.NEXT_PUBLIC_OIDC_RESPONSE_CODE,
  silent_redirect_uri: process.env.NEXT_PUBLIC_OIDC_REDIRECT_URL,
  automaticSilentRenew: true,
  onSigninCallback() {
    window.history.replaceState({}, document.title, window.location.pathname)
  }
}

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  return <AuthProviderLib {...oidcConfig}>{children}</AuthProviderLib>
}
