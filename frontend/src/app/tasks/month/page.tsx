import {
  startOfMonth,
  startOfWeek,
  lastDayOfMonth,
  lastDayOfWeek,
  format,
  eachDayOfInterval,
  isSameDay,
  isSameMonth
} from 'date-fns'
import { Box, Typography } from '@mui/joy'
import { getTasksGroupedByDate } from '../actions/getTasksGroupedByDate'
import { convertMinutesToTime } from '../utils/time'
import { SimpleTaskBox } from '../components/TaskBox'
import { Fragment } from 'react'

const WEEK_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']

export default async function MonthView() {
  const today = new Date()
  const firstDayOfFirstWeek = startOfWeek(startOfMonth(today))
  const lastDayOfLastWeek = lastDayOfWeek(lastDayOfMonth(today))
  const dateRange = eachDayOfInterval({ start: firstDayOfFirstWeek, end: lastDayOfLastWeek })

  const groupedTasks = await getTasksGroupedByDate(
    format(firstDayOfFirstWeek, 'yyyy-MM-dd'),
    format(lastDayOfLastWeek, 'yyyy-MM-dd'),
    500
  )

  return (
    <>
      <Typography sx={{ fontSize: { xs: 'lg', sm: 'xl4' } }} level="h1" textAlign="center">
        {format(today, 'MMMM yyy')}
      </Typography>
      <Box
        sx={{
          display: 'grid',
          gridTemplateColumns: 'repeat(7,minmax(0,1fr))'
        }}
        alignItems="stretch"
        height="100%"
      >
        {WEEK_DAYS.map((day, index) => {
          const isToday = index === today.getDay()
          return (
            <Typography
              sx={{
                textAlign: 'center',
                padding: { xs: '8px', sm: '22px 16px' },
                borderBottom: '1px solid #C4C6D0',
                borderTop: '1px solid #C4C6D0',
                borderRight: '1px solid #C4C6D0',
                ':first-of-type': { borderLeft: '1px solid #C4C6D0' },
                ':nth-of-type(7n)': { borderRight: { sm: 'none' }, borderLeft: { sm: 'none' } },
                fontWeight: '600',
                backgroundColor: isToday ? '#E6EFF8' : '#fff',
                color: isToday ? '#004c92' : 'auto',
                fontSize: { xs: '0px', sm: '1rem' },
                ':first-letter': { fontSize: '1rem' }
              }}
              component="div"
              key="day"
            >
              {day}
            </Typography>
          )
        })}

        {dateRange.map((date) => {
          const formattedDate = format(date, 'yyyy-MM-dd')
          const tasksFromDay = groupedTasks[formattedDate]

          return (
            <Box
              sx={{
                backgroundColor: isSameDay(date, today) ? ' #E6EFF8' : '#fff',
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
              key={formattedDate}
            >
              <Box
                sx={{
                  display: 'flex',
                  flexDirection: { xs: 'column', sm: 'row' },
                  gap: { xs: '4px ', sm: '8px' }
                }}
              >
                <Typography
                  sx={{ display: { xs: 'block', sm: 'none ' }, fontSize: 'sm' }}
                  fontWeight="600"
                >
                  {format(date, isSameMonth(date, today) ? 'dd' : 'MMM dd')}
                </Typography>
                <Typography sx={{ display: { xs: 'none', sm: 'block ' } }} fontWeight="600">
                  {format(date, isSameMonth(date, today) ? 'do' : 'MMMM do')}
                </Typography>
                <Typography
                  sx={{ fontSize: { xs: '0.7rem', sm: '1rem' } }}
                  fontWeight="600"
                  textColor="#004c92"
                >
                  {tasksFromDay?.time ? convertMinutesToTime(tasksFromDay.time) : '0h 0m'}
                </Typography>
              </Box>

              <Box
                sx={{
                  display: 'flex',
                  gap: '2px',
                  flexDirection: { xs: 'row', sm: 'column' },
                  flexWrap: 'wrap'
                }}
              >
                {tasksFromDay?.tasks.map((task) => {
                  return (
                    <Fragment key={task.id}>
                      <Box
                        sx={{
                          width: '5px',
                          height: '5px',
                          borderRadius: '50%',
                          backgroundColor: '#a8aeeb',
                          display: { xs: 'block', sm: 'none' }
                        }}
                      />
                      <Box
                        sx={{
                          display: { xs: 'none', sm: 'block' },
                          alignItems: 'center'
                        }}
                      >
                        <SimpleTaskBox task={task} />
                      </Box>
                    </Fragment>
                  )
                })}
              </Box>
            </Box>
          )
        })}
      </Box>
    </>
  )
}
