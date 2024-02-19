'use client'

import { Box, Skeleton, Typography } from '@mui/joy'
import { WeekDays } from './components/WeekDays'
import { useDateParam } from '../../hooks/useDateParam'
import { format } from 'date-fns'

export default function Loading() {
  const { date } = useDateParam()

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
        {format(date, 'MMMM yyy')}
      </Typography>
      <Box
        sx={{
          display: 'grid',
          gridTemplateColumns: 'repeat(7,minmax(0,1fr))'
        }}
        alignItems="stretch"
        height="100%"
      >
        <WeekDays />
        {Array.from(Array(35).keys()).map((_, index) => {
          return (
            <Box
              sx={{
                backgroundColor: '#fff',
                padding: { xs: '4px', sm: '22px 16px' },
                height: { xs: '77px', sm: '185px' },
                borderBottom: '1px solid #C4C6D0',
                borderRight: '1px solid #C4C6D0',
                ':nth-of-type(7n + 1)': { borderLeft: { xs: '1px solid #C4C6D0', sm: 'none' } },
                ':nth-of-type(7n)': { borderRight: { sm: 'none' }, borderLeft: { sm: 'none' } },
                display: 'flex',
                flexDirection: 'column',
                gap: '4px',
                overflow: 'auto'
              }}
              key={index}
            >
              <Box
                sx={{
                  width: '100%',
                  position: 'relative',
                  height: '27px'
                }}
              >
                <Skeleton />
              </Box>
              <Box
                sx={{
                  display: 'flex',
                  flexDirection: { xs: 'column', sm: 'row' },
                  gap: { xs: '4px ', sm: '8px' }
                }}
              >
                <Box sx={{ position: 'relative', width: '100%', height: '74px' }}>
                  <Skeleton />
                </Box>
              </Box>
            </Box>
          )
        })}
      </Box>
    </>
  )
}
