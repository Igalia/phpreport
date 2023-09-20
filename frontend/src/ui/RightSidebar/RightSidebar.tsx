'use client'

import { useState } from 'react'
import Box from '@mui/joy/Box'
import { CollapseButton } from '../CollapseButton/CollapseButton'

type RightSidebarProps = {
  children: React.ReactNode
}

export const RightSidebar = ({ children }: RightSidebarProps) => {
  const [expanded, setExpanded] = useState(false)

  return (
    <Box
      sx={{
        height: '100vh',
        width: expanded ? 320 : 60,
        transition: 'width 0.6s',
        position: 'relative',
        bgcolor: 'white',
        border: '1px solid #D2D2D4'
      }}
    >
      <CollapseButton
        sx={{
          left: '-16px',
          top: '61px',
          transform: 'rotateY(180deg)',
          ...(expanded && {
            transform: 'rotateY(0deg)'
          })
        }}
        onClick={() => setExpanded((prevState) => !prevState)}
      />
      {children}
    </Box>
  )
}
