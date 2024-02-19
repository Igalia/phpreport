import {
  startOfMonth,
  startOfWeek,
  lastDayOfMonth,
  lastDayOfWeek,
  format,
  eachDayOfInterval,
  isToday,
  isSameMonth
} from 'date-fns'
import { Typography, Box } from '@mui/joy'
import { Fragment } from 'react'
import { getTasksGroupedByDate } from '../../actions/getTasksGroupedByDate'
import { getDateFromParam, convertMinutesToTime } from '../../utils/time'
import { WeekDays } from './components/WeekDays'
import { SimpleTaskBox } from '../../components/TaskBox'

export default async function MonthView({ params }: { params: { date?: string } }) {
  const selectedDate = getDateFromParam(params.date && params.date[0])
  const firstDayOfFirstWeek = startOfWeek(startOfMonth(selectedDate))
  const lastDayOfLastWeek = lastDayOfWeek(lastDayOfMonth(selectedDate))
  const dateRange = eachDayOfInterval({ start: firstDayOfFirstWeek, end: lastDayOfLastWeek })

  const groupedTasks = await getTasksGroupedByDate(
    format(firstDayOfFirstWeek, 'yyyy-MM-dd'),
    format(lastDayOfLastWeek, 'yyyy-MM-dd'),
    500
  )

  return (
    <>
      <Typography
        sx={{ fontSize: { xs: 'lg', sm: 'xl4' }, margin: '0 auto 16px' }}
        level="h1"
        textAlign="center"
      >
        {format(selectedDate, 'MMMM yyy')}
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

        {dateRange.map((date) => {
          const formattedDate = format(date, 'yyyy-MM-dd')
          const tasksFromDay = groupedTasks[formattedDate]

          return (
            <Box
              sx={{
                backgroundColor: isToday(date) ? ' #E6EFF8' : '#fff',
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
                  {format(date, isSameMonth(date, selectedDate) ? 'dd' : 'MMM dd')}
                </Typography>
                <Typography sx={{ display: { xs: 'none', sm: 'block ' } }} fontWeight="600">
                  {format(date, isSameMonth(date, selectedDate) ? 'do' : 'MMMM do')}
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
