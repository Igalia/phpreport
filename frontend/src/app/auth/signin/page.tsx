'use client'

import Button from '@mui/joy/Button'
import { signIn } from 'next-auth/react'

export default function SignIn() {
  return <Button onClick={() => signIn('keycloack')}>Sign In with Igalia</Button>
}
