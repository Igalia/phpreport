import './globals.css'
import type { Metadata } from 'next'
import localFont from 'next/font/local'
import { BaseLayout } from '@/ui/BaseLayout/BaseLayout'
import { Providers } from './providers'

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
        <Providers>
          <BaseLayout>{children}</BaseLayout>
        </Providers>
      </body>
    </html>
  )
}
