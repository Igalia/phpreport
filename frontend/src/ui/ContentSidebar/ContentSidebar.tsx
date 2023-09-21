'use client'

import { useState } from 'react'
import Box from '@mui/joy/Box'
import { CollapseButton } from '../CollapseButton/CollapseButton'

type ContentSidebarProps = {
  children: React.ReactNode
}

export const ContentSidebar = ({ children }: ContentSidebarProps) => {
  const [expanded, setExpanded] = useState(false)

  return (
    <Box
      sx={{
        height: { xs: expanded ? '60px' : '320px', sm: '100vh' },
        width: { xs: '100vw', sm: expanded ? '60px' : '320px' },
        transition: { xs: 'height 0.6s', sm: 'width 0.6s' },
        position: 'relative',
        bgcolor: 'white',
        border: '1px solid #D2D2D4'
      }}
    >
      <CollapseButton
        sx={{
          left: { xs: '0px', sm: '-16px' },
          right: { xs: '0px', sm: 'unset' },
          top: { xs: '-16px', sm: '61px' },
          margin: '0 auto',
          transform: { xs: 'rotateZ(90deg)', sm: 'rotateZ(180deg)' },
          ...(expanded && {
            transform: { xs: 'rotateZ(-90deg)', sm: 'rotateZ(0deg)' }
          })
        }}
        onClick={() => setExpanded((prevState) => !prevState)}
      />
      {children}
    </Box>
  )
}
