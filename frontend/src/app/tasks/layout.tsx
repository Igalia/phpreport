'use client'

import { PropsWithChildren } from 'react'
import { Tabs, TabList, Tab, Box, Divider } from '@mui/joy'
import Link from 'next/link'
import { usePathname } from 'next/navigation'

export default function TasksLayout({ children }: PropsWithChildren) {
  const pathname = usePathname()

  return (
    <Box
      sx={{
        display: 'grid',
        padding: { xs: '0 8px', sm: '0' },
        margin: '0 auto',
        maxWidth: '1146px',
        rowGap: '16px'
      }}
    >
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
      <Divider />
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
