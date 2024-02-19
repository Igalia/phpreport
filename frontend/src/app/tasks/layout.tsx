'use client'
import { useState } from 'react'

import { PropsWithChildren } from 'react'
import { Tabs, TabList, Tab, Box, Button, Typography } from '@mui/joy'
import Link from 'next/link'
import { ContentSidebar } from '@/ui/ContentSidebar/ContentSidebar'
import { WorkSummaryPanel } from '@/app/tasks/components/WorkSummaryPanel'

import { usePathname, useRouter } from 'next/navigation'
import {
  isSameDay,
  format,
  addDays,
  subDays,
  addWeeks,
  subWeeks,
  addMonths,
  subMonths
} from 'date-fns'
import { useDateParam } from './hooks/useDateParam'
import {
  ChevronLeft16Regular,
  ChevronRight16Regular,
  Calendar16Regular
} from '@fluentui/react-icons'
import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'

export default function TasksLayout({ children }: PropsWithChildren) {
  const [contentBarExpanded, setContentBarExpanded] = useState(false)

  const pathname = usePathname()
  const { dateParam } = useDateParam()

  const selectedTabValue = dateParam ? pathname : `${pathname}/`

  return (
    <Box
      sx={{
        margin: '0 60px 0 auto'
      }}
    >
      <Box
        sx={{
          maxWidth: '1146px',
          margin: '0 auto 16px',
          width: '100%',
          display: 'flex',
          alignItems: 'center',
          height: '33px',
          gap: '8px'
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
          value={selectedTabValue}
        >
          <TabList disableUnderline>
            <TimeViewTab path={`/tasks/day/${dateParam}`}>Day</TimeViewTab>
            <TimeViewTab path={`/tasks/week/${dateParam}`}>Week</TimeViewTab>
            <TimeViewTab path={`/tasks/month/${dateParam}`}>Month</TimeViewTab>
          </TabList>
        </Tabs>
        <DateSelector />
      </Box>
      {children}
      <ContentSidebar
        expanded={contentBarExpanded}
        toggleContentBar={() => setContentBarExpanded((prevState) => !prevState)}
      >
        <WorkSummaryPanel contentSidebarExpanded={contentBarExpanded} />
      </ContentSidebar>
    </Box>
  )
}

const DateSelector = () => {
  const { date } = useDateParam()
  const pathname = usePathname()
  const router = useRouter()

  const formattedDate = () => {
    if (pathname.includes('month') || pathname.includes('week')) {
      return format(date, 'MMMM, yyyy')
    }

    if (isSameDay(date, new Date())) return `Today, ${format(new Date(), 'MMM dd')}`

    return format(date, 'cccc, MMM dd')
  }

  const nextDate = () => {
    if (pathname.includes('week')) {
      return `/tasks/week/${format(addWeeks(date, 1), 'yyyy-MM-dd')}`
    }

    if (pathname.includes('month')) {
      return `/tasks/month/${format(addMonths(date, 1), 'yyyy-MM')}`
    }

    return `/tasks/day/${format(addDays(date, 1), 'yyyy-MM-dd')}`
  }

  const previousDate = () => {
    if (pathname.includes('week')) {
      return `/tasks/week/${format(subWeeks(date, 1), 'yyyy-MM-dd')}`
    }

    if (pathname.includes('month')) {
      return `/tasks/month/${format(subMonths(date, 1), 'yyyy-MM-dd')}`
    }

    return `/tasks/day/${format(subDays(date, 1), 'yyyy-MM-dd')}`
  }

  return (
    <Box
      sx={{
        borderRadius: '8px',
        border: '1px solid #C4C6D0',
        height: '100%',
        display: 'flex',
        alignItems: 'center',
        gap: '8px'
      }}
    >
      <Button
        sx={{
          background: '#C4C6D0',
          ':hover': { background: '#C4C6D0' },
          ':active': { background: '#DCDCE5' },
          borderRadius: '8px 0 0 8px'
        }}
        onClick={() => router.push(previousDate())}
      >
        <ChevronLeft16Regular color="black" />
        <ScreenReaderOnly>Select Previous Date</ScreenReaderOnly>
      </Button>
      <Box sx={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
        <Calendar16Regular />
        <Typography>{formattedDate()}</Typography>
      </Box>
      <Button
        sx={{
          background: '#C4C6D0',
          ':hover': { background: '#C4C6D0' },
          ':active': { background: '#DCDCE5' },
          borderRadius: '0 8px 8px 0'
        }}
        onClick={() => router.push(nextDate())}
      >
        <ChevronRight16Regular color="black" />
        <ScreenReaderOnly>Select Next Date</ScreenReaderOnly>
      </Button>
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
