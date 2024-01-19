import { makeGetTasks } from '@/infra/task/getTasks'
import { serverFetch } from '@/infra/lib/serverFetch'
import { authOptions } from '@/app/api/auth/[...nextauth]/route'
import { getServerSession } from 'next-auth'
import { format, eachDayOfInterval, startOfWeek, add, isSameDay } from 'date-fns'
import { Task } from '@/domain/Task'
import { Typography, Box, Stack } from '@mui/joy'
import { SimpleTaskBox } from '../components/TaskBox'
import { convertTimeToMinutes, convertMinutesToTime } from '../utils/time'

const getTasks = async (startTime: string, endTime: string) => {
  const apiClient = await serverFetch()
  const getTasks = makeGetTasks(apiClient)
  const session = await getServerSession(authOptions)
  const { id: userId } = session!.user

  const tasks = await getTasks({ userId, startTime, endTime })

  // Group tasks by date and add the total time for that date
  return tasks.reduce<Record<Task['date'], { tasks: Array<Task>; time: number }>>((acc, task) => {
    const startTime = convertTimeToMinutes(task.startTime)
    const endTime = convertTimeToMinutes(task.endTime)
    const timeDiff = endTime - startTime

    if (acc[task.date]) {
      const { tasks, time } = acc[task.date]

      return { ...acc, [task.date]: { tasks: [...tasks, task], time: time + timeDiff } }
    }

    return { ...acc, [task.date]: { tasks: [task], time: timeDiff } }
  }, {})
}

export default async function WeekView() {
  const today = new Date()
  const firstDayofWeek = startOfWeek(today)
  const lastDayOfWeek = add(firstDayofWeek, { days: 6 })
  const dateRange = eachDayOfInterval({ start: firstDayofWeek, end: lastDayOfWeek })
  const groupedTasks = await getTasks(
    format(firstDayofWeek, 'yyyy-MM-dd'),
    format(lastDayOfWeek, 'yyyy-MM-dd')
  )

  return (
    <Stack direction="row" alignItems="stretch" height="100%">
      {dateRange.map((date) => {
        const formattedDate = format(date, 'eeee, do')
        const dateTasks = groupedTasks[format(date, 'yyyy-MM-dd')]

        return (
          <Box
            sx={{
              backgroundColor: isSameDay(date, today) ? ' #E6EFF8' : '#fff',
              flex: '1',
              ':first-of-type': {
                borderLeft: '1px solid #C4C6D0'
              },
              borderTop: '1px solid #C4C6D0',
              borderBottom: '1px solid #C4C6D0',
              borderRight: '1px solid #C4C6D0',
              display: 'flex',
              flexDirection: 'column',
              minHeight: 'calc(100vh - 79px)',
              gap: '8px'
            }}
            component="ul"
            key={formattedDate}
          >
            <Box padding="22px 16px" borderBottom="1px solid #C4C6D0">
              <Typography fontWeight="600">{formattedDate}</Typography>
              <Typography fontWeight="600" textColor="#004c92">
                {dateTasks?.time ? convertMinutesToTime(dateTasks.time) : '0h 0m'}
              </Typography>
            </Box>

            {dateTasks?.tasks.map((task) => {
              return (
                <Box sx={{ padding: '0px 8px' }} key={task.id}>
                  <SimpleTaskBox task={task} />
                </Box>
              )
            })}
          </Box>
        )
      })}
    </Stack>
  )
}
