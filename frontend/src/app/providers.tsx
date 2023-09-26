'use client'

import { CssVarsProvider } from '@mui/joy/styles'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AuthProvider } from '@/app/auth/AuthProvider'
import { theme } from '@/ui/theme'

const queryClient = new QueryClient()

export const Providers = ({ children }: { children: React.ReactNode }) => {
  return (
    <AuthProvider>
      <QueryClientProvider client={queryClient}>
        <CssVarsProvider theme={theme}>{children}</CssVarsProvider>
      </QueryClientProvider>
    </AuthProvider>
  )
}
