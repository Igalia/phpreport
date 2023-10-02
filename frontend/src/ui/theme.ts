'use client'

import { extendTheme } from '@mui/joy/styles'

export const theme = extendTheme({
  fontFamily: {
    display: 'inherit', // applies to `h1`–`h4`
    body: 'inherit' // applies to `title-*` and `body-*`
  },
  components: {
    JoyAutocomplete: {
      styleOverrides: {
        root: () => ({
          height: '56px',
          borderRadius: '8px'
        })
      }
    },
    JoyInput: {
      styleOverrides: {
        root: () => ({
          height: '56px',
          borderRadius: '8px'
        })
      }
    }
  }
})
