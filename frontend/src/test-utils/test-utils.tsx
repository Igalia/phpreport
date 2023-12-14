import React, { ReactElement } from 'react'
import { render, RenderOptions } from '@testing-library/react'

import { CssVarsProvider } from '@mui/joy/styles'
import { AlertProvider } from '@/ui/Alert/AlertProvider'
import { theme } from '@/ui/theme'
import userEvent, { Options } from '@testing-library/user-event'

const AllTheProviders = ({ children }: { children: React.ReactNode }) => {
  return (
    <AlertProvider>
      <CssVarsProvider theme={theme}>{children}</CssVarsProvider>
    </AlertProvider>
  )
}

const customRender = (ui: ReactElement, options?: Omit<RenderOptions, 'wrapper'>) =>
  render(ui, { wrapper: AllTheProviders, ...options })

function renderWithUser(jsx: ReactElement, options?: Options) {
  return {
    user: userEvent.setup(options),
    ...customRender(jsx)
  }
}

export * from '@testing-library/react'
export { customRender as render, renderWithUser }
