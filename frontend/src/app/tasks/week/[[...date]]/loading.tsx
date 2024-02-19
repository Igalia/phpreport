'use client'

import { Typography, Stack, Box, Skeleton } from '@mui/joy'
import { useDateParam } from '../../hooks/useDateParam'
import { format, startOfWeek, add, eachDayOfInterval, isToday } from 'date-fns'

export default function Loading() {
  const { date } = useDateParam()
  const firstDayofWeek = startOfWeek(date)
  const lastDayOfWeek = add(firstDayofWeek, { days: 6 })
  const dateRange = eachDayOfInterval({ start: firstDayofWeek, end: lastDayOfWeek })

  return (
    <>
      <Typography
        sx={{
          fontSize: { xs: 'lg', sm: 'xl4' },
          margin: '0 auto 16px'
        }}
        level="h1"
        textAlign="center"
      >
        {format(firstDayofWeek, 'MMMM dd, yyyy')} - {format(lastDayOfWeek, 'MMMM dd, yyyy')}
      </Typography>
      <Stack sx={{ flexDirection: { xs: 'column', sm: 'row' } }} alignItems="stretch" height="100%">
        {dateRange.map((date) => {
          const formattedDate = format(date, 'eeee, do')

          return (
            <Box
              sx={{
                backgroundColor: isToday(date) ? ' #E6EFF8' : '#fff',
                flex: '1',
                borderTop: '1px solid #C4C6D0',
                borderBottom: { xs: 'none', sm: '1px solid #C4C6D0' },
                borderRight: '1px solid #C4C6D0',
                borderLeft: { xs: '1px solid #C4C6D0', sm: 'none' },
                display: 'flex',
                flexDirection: { xs: 'row', sm: 'column' },
                minHeight: { xs: 'auto', sm: 'calc(100vh - 79px)' },
                minWidth: { xs: 'calc(100vw - 16px)', sm: 'auto' },
                alignItems: { xs: 'stretch', sm: 'initial' },
                overflow: 'auto',
                gap: '8px',
                ':last-of-type': {
                  borderBottom: '1px solid #C4C6D0',
                  borderRight: { xs: '1px solid #C4C6D0', sm: 'none' }
                }
              }}
              component="ul"
              key={formattedDate}
            >
              <Box
                sx={{
                  padding: '22px 16px',
                  width: { xs: '160px', sm: 'auto' },
                  minWidth: { xs: '160px', sm: 'auto' },
                  borderBottom: { xs: 'none', sm: '1px solid #C4C6D0' },
                  borderRight: { xs: '1px solid #C4C6D0', sm: 'none' }
                }}
              >
                <Typography fontWeight="600">{formattedDate}</Typography>
                <Typography fontWeight="600" textColor="#004c92">
                  0h 0m
                </Typography>
              </Box>
              <Box
                sx={{
                  margin: { xs: '8px 0', sm: '0px 8px' },
                  display: 'flex',
                  minWidth: { xs: 'fit-content', sm: 'auto' },
                  alignItems: 'center',
                  position: 'relative',
                  height: '74px'
                }}
              >
                <Skeleton />
              </Box>
            </Box>
          )
        })}
      </Stack>
    </>
  )
}
