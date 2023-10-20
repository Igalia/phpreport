'use client'

import { CssVarsProvider } from '@mui/joy/styles'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { SessionProvider } from 'next-auth/react'
import { theme } from '@/ui/theme'
import { AlertProvider } from '@/ui/Alert/AlertProvider'

const queryClient = new QueryClient()

export const Providers = ({ children }: { children: React.ReactNode }) => {
  return (
    <SessionProvider basePath="/web/v2/api/auth">
      <QueryClientProvider client={queryClient}>
        <AlertProvider>
          <CssVarsProvider theme={theme}>{children}</CssVarsProvider>
        </AlertProvider>
      </QueryClientProvider>
    </SessionProvider>
  )
}
