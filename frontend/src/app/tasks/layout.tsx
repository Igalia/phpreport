'use client'
import { useState } from 'react'

import { PropsWithChildren } from 'react'
import { Tabs, TabList, Tab, Box } from '@mui/joy'
import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { ContentSidebar } from '@/ui/ContentSidebar/ContentSidebar'
import { WorkSummaryPanel } from '@/app/tasks/components/WorkSummaryPanel';


export default function TasksLayout({ children }: PropsWithChildren) {
  const [contentBarExpanded, setContentBarExpanded] = useState(false)

  const pathname = usePathname()

  return (
    <Box
      sx={{
        margin: '0 60px 0 auto'
      }}
    >
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
              padding: '1px'
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
      <ContentSidebar
        expanded={contentBarExpanded}
        toggleContentBar={() => setContentBarExpanded((prevState) => !prevState)}
      >
        <WorkSummaryPanel contentSidebarExpanded={contentBarExpanded}/>
      </ContentSidebar>
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
