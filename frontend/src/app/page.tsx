'use client'

import { useEffect } from 'react'
import { useAuth, hasAuthParams } from 'react-oidc-context'
import styles from './page.module.css'

export default function Home() {
  const auth = useAuth()

  useEffect(() => {
    if (!hasAuthParams() && !auth.isAuthenticated && !auth.activeNavigator && !auth.isLoading) {
      auth.signinRedirect()
    }
  }, [auth.isAuthenticated, auth.activeNavigator, auth.isLoading, auth.signinRedirect, auth])

  switch (auth.activeNavigator) {
    case 'signinSilent':
      return <div>Signing you in...</div>
    case 'signoutRedirect':
      return <div>Signing you out...</div>
  }

  if (auth.isLoading) {
    return <div>Loading...</div>
  }

  if (auth.error) {
    return <div>Oops... {auth.error.message}</div>
  }
  return <main className={styles.main}>Testing auth</main>
}
