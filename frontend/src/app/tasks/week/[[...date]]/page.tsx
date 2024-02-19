import { format, eachDayOfInterval, startOfWeek, add, isToday } from 'date-fns'
import { Typography, Box, Stack } from '@mui/joy'
import { SimpleTaskBox } from '../../components/TaskBox'
import { convertMinutesToTime, getDateFromParam } from '../../utils/time'

import { getTasksGroupedByDate } from '../../actions/getTasksGroupedByDate'

export default async function WeekView({ params }: { params: { date?: string } }) {
  const selectedDate = getDateFromParam(params.date && params.date[0])
  const firstDayofWeek = startOfWeek(selectedDate)
  const lastDayOfWeek = add(firstDayofWeek, { days: 6 })
  const dateRange = eachDayOfInterval({ start: firstDayofWeek, end: lastDayOfWeek })
  const groupedTasks = await getTasksGroupedByDate(
    format(firstDayofWeek, 'yyyy-MM-dd'),
    format(lastDayOfWeek, 'yyyy-MM-dd')
  )

  return (
    <>
      <Typography
        sx={{ fontSize: { xs: 'lg', sm: 'xl4' }, margin: '0 auto 16px' }}
        level="h1"
        textAlign="center"
      >
        {format(firstDayofWeek, 'MMMM dd, yyyy')} - {format(lastDayOfWeek, 'MMMM dd, yyyy')}
      </Typography>
      <Stack sx={{ flexDirection: { xs: 'column', sm: 'row' } }} alignItems="stretch" height="100%">
        {dateRange.map((date) => {
          const formattedDate = format(date, 'eeee, do')
          const dateTasks = groupedTasks[format(date, 'yyyy-MM-dd')]

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
                  {dateTasks?.time ? convertMinutesToTime(dateTasks.time) : '0h 0m'}
                </Typography>
              </Box>

              {dateTasks?.tasks.map((task) => {
                return (
                  <Box
                    sx={{
                      padding: { xs: '8px 0', sm: '0px 8px' },
                      display: 'flex',
                      minWidth: { xs: 'fit-content', sm: 'auto' },
                      alignItems: 'center'
                    }}
                    component="li"
                    key={task.id}
                  >
                    <SimpleTaskBox task={task} />
                  </Box>
                )
              })}
            </Box>
          )
        })}
      </Stack>
    </>
  )
}
