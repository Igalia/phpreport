'use client'

import { useEffect } from 'react'
import { useAuth, hasAuthParams } from 'react-oidc-context'

export const AuthorizedPage = ({ children }: { children: React.ReactNode }) => {
  const { isAuthenticated, signinRedirect, activeNavigator, error, isLoading } = useAuth()

  useEffect(() => {
    if (!isAuthenticated && !hasAuthParams()) {
      signinRedirect()
    }
  }, [isAuthenticated, signinRedirect])

  switch (activeNavigator) {
    case 'signinSilent':
      return <div>Signing you in...</div>
    case 'signoutRedirect':
      return <div>Signing you out...</div>
  }

  if (isLoading) {
    return <div>Loading...</div>
  }

  if (error) {
    return <div>Oops... {error.message}</div>
  }

  if (!isAuthenticated) {
    return <button onClick={() => signinRedirect()}>BOTAO</button>
  }

  return children
}
