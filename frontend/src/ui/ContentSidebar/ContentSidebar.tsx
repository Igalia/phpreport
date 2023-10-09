'use client'

import Box from '@mui/joy/Box'
import { CollapseButton } from '../CollapseButton/CollapseButton'

type ContentSidebarProps = {
  children: React.ReactNode
  expanded: boolean
  toggleContentBar: () => void
}

export const ContentSidebar = ({ children, expanded, toggleContentBar }: ContentSidebarProps) => {
  return (
    <Box
      sx={{
        height: { xs: expanded ? '60px' : '320px', sm: '100vh' },
        width: { xs: '100vw', sm: expanded ? '320px' : '60px' },
        transition: { xs: 'height 0.6s', sm: 'width 0.6s' },
        bgcolor: 'white',
        border: '1px solid #D2D2D4',
        position: { xs: 'relative', sm: 'fixed' },
        zIndex: 1,
        top: { xs: 'unset', sm: '0' },
        right: 0
      }}
    >
      <CollapseButton
        sx={{
          left: { xs: '0px', sm: '-16px' },
          right: { xs: '0px', sm: 'unset' },
          top: { xs: '-16px', sm: '61px' },
          margin: '0 auto'
        }}
        iconSx={{
          transform: { xs: 'rotateZ(90deg)', sm: 'rotateZ(180deg)' },
          ...(expanded && {
            transform: { xs: 'rotateZ(-90deg)', sm: 'rotateZ(0deg)' }
          })
        }}
        onClick={toggleContentBar}
      />
      {children}
    </Box>
  )
}
