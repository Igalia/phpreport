'use client'

import { useState } from 'react'
import Stack from '@mui/joy/Stack'
import Typography from '@mui/joy/Typography'
import Box from '@mui/joy/Box'
import Image from 'next/image'
import {
  TaskListSquareAdd24Filled,
  Beach24Filled,
  DataArea24Filled,
  DataPie24Filled
} from '@fluentui/react-icons'
import smallLogo from '@/assets/images/small_logo.png'
import fullLogo from '@/assets/images/full_logo.png'
import { styled } from '@mui/joy/styles'
import Link from 'next/link'
import { DarkModeSwitch } from './DarkModeSwitch'
import { CollapseButton } from '../CollapseButton/CollapseButton'
import { SxProps } from '@mui/joy/styles/types'

export const Sidebar = () => {
  const [expanded, setExpanded] = useState(false)

  return (
    <Box
      sx={{
        height: { xs: expanded ? '73px' : '280px', sm: '100vh' },
        width: { xs: '100vw', sm: expanded ? 336 : 73 },
        transition: { xs: 'height 0.6s', sm: 'width 0.6s' },
        position: 'relative',
        bgcolor: '#001C37',
        padding: '22px 16px 18px'
      }}
      component="aside"
    >
      <CollapseButton
        sx={{
          right: { xs: '0px', sm: '-16px' },
          left: { xs: '0px', sm: 'unset' },
          top: { xs: 'unset', sm: '61px' },
          bottom: { xs: '-16px', sm: 'unset' },
          margin: '0 auto',
          transform: { xs: 'rotateZ(-90deg)', sm: 'rotateZ(0deg)' },
          ...(expanded && {
            transform: { xs: 'rotateZ(90deg)', sm: 'rotateZ(180deg)' }
          })
        }}
        onClick={() => setExpanded((prevState) => !prevState)}
      />
      <Box sx={navGridStyle} component="nav">
        <Logo height={32} src={fullLogo} alt="Igalia Logo" />
        <Stack
          sx={{
            svg: { minWidth: '32px' },
            whiteSpace: 'nowrap',
            padding: '0 4px',
            listStyleType: 'none',
            gridArea: 'nav'
          }}
          component="ul"
          spacing={2}
        >
          <Box component="li">
            <NavLink href="/tasks">
              <TaskListSquareAdd24Filled />
              <Typography textColor="white">Tasks</Typography>
            </NavLink>
          </Box>
          <Box component="li">
            <NavLink href="/vacation">
              <Beach24Filled />
              <Typography textColor="white">Vacation Management</Typography>
            </NavLink>
          </Box>
          <Box component="li">
            <NavLink href="/reports">
              <DataArea24Filled />
              <Typography textColor="white">Reports</Typography>
            </NavLink>
          </Box>
          <Box component="li">
            <NavLink href="/data-managment">
              <DataPie24Filled />
              <Typography textColor="white">Data Management</Typography>
            </NavLink>
          </Box>
        </Stack>
        <DarkModeSwitch
          sx={{
            mt: 'auto',
            justifySelf: { xs: 'flex-end', sm: 'flex-start' },
            transition: 'transform 0.6s',
            transformOrigin: '20px',
            mr: '4px',
            gridArea: 'switch',
            ...(!expanded && {
              transform: { sm: 'rotate(-90deg)' }
            })
          }}
        />
      </Box>
    </Box>
  )
}

const Logo = styled(Image)`
  margin-bottom: 50px;
  grid-area: 'logo';
  padding: 0 4px;
`

const NavLink = styled(Link)`
  display: flex;
  gap: 30px;
  align-items: center;
`

const navGridStyle: SxProps = {
  overflow: 'hidden',
  height: '100%',
  display: 'grid',
  gridTemplateAreas: {
    xs: `
      'logo switch'
      'nav nav'`,
    sm: `
      'logo'
      'nav'
      'switch'
    `
  },
  gridTemplateRows: '32px 1fr',
  gridTemplateColumns: '50%',
  rowGap: '20px'
}
