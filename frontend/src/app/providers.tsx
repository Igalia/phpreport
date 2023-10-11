'use client'

import { CssVarsProvider } from '@mui/joy/styles'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AuthProvider } from '@/app/auth/AuthProvider'
import { theme } from '@/ui/theme'
import { AlertProvider } from '@/ui/Alert/AlertProvider'

const queryClient = new QueryClient()

export const Providers = ({ children }: { children: React.ReactNode }) => {
  return (
    <AuthProvider>
      <QueryClientProvider client={queryClient}>
        <AlertProvider>
          <CssVarsProvider theme={theme}>{children}</CssVarsProvider>
        </AlertProvider>
      </QueryClientProvider>
    </AuthProvider>
  )
}
