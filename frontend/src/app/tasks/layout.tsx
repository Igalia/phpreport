'use client'

import { PropsWithChildren } from 'react'
import { Tabs, TabList, Tab, Box } from '@mui/joy'
import Link from 'next/link'
import { usePathname } from 'next/navigation'

export default function TasksLayout({ children }: PropsWithChildren) {
  const pathname = usePathname()

  return (
    <Box
      sx={{
        display: 'flex',
        padding: { xs: '0 8px', sm: '0' },
        gridTemplateRows: '33px 1fr',
        margin: '0 auto',
        minHeight: 'calc(100vh - 30px)',
        rowGap: '16px',
        flexDirection: 'column'
      }}
    >
      <Box sx={{ maxWidth: '1146px', margin: '0 auto', width: '100%' }}>
        <Tabs
          sx={{
            borderRadius: '8px',
            border: '1px solid #C4C6D0',
            width: 'fit-content',
            paddding: '1px'
          }}
          size="md"
          value={pathname}
        >
          <TabList disableUnderline>
            <TimeViewTab path="/tasks">Day</TimeViewTab>
            <TimeViewTab path="/tasks/week">Week</TimeViewTab>
            <TimeViewTab path="/tasks/month">Month</TimeViewTab>
          </TabList>
        </Tabs>
      </Box>
      {children}
    </Box>
  )
}

const TimeViewTab = ({ children, path }: PropsWithChildren<{ path: string }>) => {
  return (
    <Link href={path}>
      <Tab
        sx={{ borderRadius: '7px' }}
        value={path}
        variant="plain"
        color="primary"
        disableIndicator
      >
        {children}
      </Tab>
    </Link>
  )
}
