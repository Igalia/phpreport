'use client'

import { useState } from 'react'
import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'
import Typography from '@mui/joy/Typography'
import Box from '@mui/joy/Box'
import Image from 'next/image'
import {
  ChevronRight16Filled,
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
import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'

export const Sidebar = () => {
  const [expanded, setExpanded] = useState(false)

  return (
    <Box
      sx={{
        height: '100vh',
        width: expanded ? 336 : 73,
        transition: 'width 0.6s',
        position: 'relative',
        bgcolor: '#001C37',
        padding: '22px 16px 18px'
      }}
      component="aside"
    >
      <Button
        size="sm"
        onClick={() => setExpanded((prevState) => !prevState)}
        sx={{
          borderRadius: '50%',
          bgcolor: 'white',
          width: 32,
          height: 32,
          alignItems: 'center',
          justifyContent: 'center',
          display: 'flex',
          position: 'absolute',
          right: '-16px',
          top: '61px',
          padding: 0,
          zIndex: 1,
          boxShadow: '0px 6px 6px #00000029',
          ...(expanded && {
            transform: 'rotateY(180deg)'
          })
        }}
      >
        <ChevronRight16Filled primaryFill="black" />
        <ScreenReaderOnly>Expand/collapse menu</ScreenReaderOnly>
      </Button>
      <Stack sx={{ overflow: 'hidden', height: '100%' }} component="nav">
        <Logo height={32} src={expanded ? fullLogo : smallLogo} alt="Igalia Logo" />
        <Stack
          sx={{
            svg: { minWidth: '32px' },
            whiteSpace: 'nowrap',
            padding: '0 4px',
            listStyleType: 'none'
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
            alignSelf: 'flex-start',
            transition: 'transform 0.6s',
            transformOrigin: '20px',
            ...(!expanded && {
              transform: 'rotate(-90deg)'
            })
          }}
        />
      </Stack>
    </Box>
  )
}

const Logo = styled(Image)`
  margin-bottom: 50px;
`

const NavLink = styled(Link)`
  display: flex;
  gap: 30px;
  align-items: center;
`
