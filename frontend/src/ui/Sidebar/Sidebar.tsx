'use client'

import Stack from '@mui/joy/Stack'
import Typography from '@mui/joy/Typography'
import Box from '@mui/joy/Box'
import Image from 'next/image'
import { TaskListSquareAdd24Filled, DataPie24Filled, Open24Filled } from '@fluentui/react-icons'
import fullLogo from '@/assets/images/full_logo.png'
import { styled } from '@mui/joy/styles'
import Link from 'next/link'
import { DarkModeSwitch } from './DarkModeSwitch'
import { CollapseButton } from '../CollapseButton/CollapseButton'
import { SxProps } from '@mui/joy/styles/types'

type SidebarProps = {
  expanded: boolean
  toggleSidebar: () => void
}

function getSupportLinks() {
  const urls = (process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_URL ?? '')
    .split(',')
    .map((s) => s.trim())
  const texts = (process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_TEXT ?? '')
    .split(',')
    .map((s) => s.trim())

  return urls
    .map((url, i) => ({ url, text: texts[i] || url }))
    .filter((link) => link.url)
}

export const Sidebar = ({ expanded, toggleSidebar }: SidebarProps) => {
  const supportLinks = getSupportLinks()

  return (
    <Box
      sx={{
        height: { xs: expanded ? '280px' : '73px', sm: '100%' },
        width: { xs: '100vw', sm: expanded ? 336 : 73 },
        transition: { xs: 'height 0.6s', sm: 'width 0.6s' },
        position: 'fixed',
        zIndex: 999,
        top: 0,
        left: 0,
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
          margin: '0 auto'
        }}
        iconSx={{
          transform: { xs: 'rotateZ(90deg)', sm: 'rotateZ(0deg)' },
          ...(expanded && {
            transform: { xs: 'rotateZ(-90deg)', sm: 'rotateZ(180deg)' }
          })
        }}
        onClick={toggleSidebar}
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
            <NavLink href="/tasks/day">
              <TaskListSquareAdd24Filled color="white" />
              <Typography textColor="white">Tasks</Typography>
            </NavLink>
          </Box>
          <Box component="li">
            <NavLink href="/planner">
              <DataPie24Filled color="white" />
              <Typography textColor="white">Project Planner</Typography>
            </NavLink>
          </Box>
        </Stack>
        {supportLinks.length > 0 && (
          <Stack
            sx={{
              svg: { minWidth: '32px' },
              whiteSpace: 'nowrap',
              padding: '0 4px',
              listStyleType: 'none',
              gridArea: 'links'
            }}
            component="ul"
            spacing={2}
          >
            {supportLinks.map((link) => (
              <Box component="li" key={link.url}>
                <ExternalLink href={link.url} target="_blank" rel="noopener noreferrer">
                  <Open24Filled color="white" />
                  <Typography textColor="white">{link.text}</Typography>
                </ExternalLink>
              </Box>
            ))}
          </Stack>
        )}
        <DarkModeSwitch
          sx={{
            mt: 'auto',
            justifySelf: { xs: 'flex-end', sm: 'flex-start' },
            transition: 'transform 0.6s',
            transformOrigin: '20px',
            mr: '4px',
            mb: '9px',
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

const ExternalLink = styled('a')`
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
      'nav nav'
      'links links'`,
    sm: `
      'logo'
      'nav'
      'links'
      'switch'
    `
  },
  gridTemplateRows: '32px 1fr',
  gridTemplateColumns: '50%',
  rowGap: '20px'
}
