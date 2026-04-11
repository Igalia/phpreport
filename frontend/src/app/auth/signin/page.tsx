'use client'

import { useSearchParams } from 'next/navigation'
import Button from '@mui/joy/Button'
import { signIn } from 'next-auth/react'

export default function SignIn() {
  const searchParams = useSearchParams()
  const callbackUrl = searchParams.get('callbackUrl') || '/tasks'

  return (
    <Button onClick={() => signIn('keycloak', { callbackUrl })}>Sign In with Igalia</Button>
  )
}
