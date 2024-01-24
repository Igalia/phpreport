'use client'
import { useState } from 'react'
import { styled } from '@mui/joy/styles'
import { Sidebar } from '@/ui/Sidebar/Sidebar'
import { Alert } from '@/ui/Alert/Alert'


export const BaseLayout = ({ children }: { children: React.ReactNode }) => {
  const [navBarExpanded, setNavBarExpanded] = useState(false)
  return (
    <>
      <SkipNavigation href="#main-content">Skip Navigation</SkipNavigation>
      <Sidebar
        expanded={navBarExpanded}
        toggleSidebar={() => setNavBarExpanded((prevState) => !prevState)}
      />
      <Main
        sx={{
          transition: 'margin .6s',
          ml: { sm: navBarExpanded ? '336px' : '73px' },
          mt: { xs: navBarExpanded ? '280px' : '73px', sm: '0' }
        }}
        id="main-content"
        tabIndex={-1}
      >
        {children}
      </Main>
      <Alert />
    </>
  )
}

const SkipNavigation = styled('a')`
  left: -10000px;
  position: absolute;
  z-index: 1;

  &:focus {
    left: 0;
    right: 0;
    top: 5px;
    margin: 0 auto;
    width: fit-content;
  }
`

const Main = styled('main')`
  width: 100%;
  height: 100%;
  padding: 30px 0 0;

  &:focus {
    outline: none;
  }
`
