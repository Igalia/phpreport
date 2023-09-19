import './globals.css'
import type { Metadata } from 'next'
import localFont from 'next/font/local'
import { AuthProvider } from '@/app/auth/AuthProvider'
import { Sidebar } from '@/ui/Sidebar/Sidebar'
import { CssVarsProvider } from '@mui/joy/styles'
import { theme } from '@/ui/theme'

const monaSans = localFont({
  src: '../assets/fonts/Mona-Sans.woff2',
  display: 'swap'
})

export const metadata: Metadata = {
  title: 'PhpReport',
  description: 'Time tracking for projects'
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body className={monaSans.className}>
        <AuthProvider>
          <CssVarsProvider theme={theme}>
            <Sidebar />
            {children}
          </CssVarsProvider>
        </AuthProvider>
      </body>
    </html>
  )
}
