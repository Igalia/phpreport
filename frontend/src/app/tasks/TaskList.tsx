'use client'

import Box from '@mui/joy/Box'
import Typography from '@mui/joy/Typography'

import { useGetTasks } from './hooks/useTask'

type TaskListProps = {
  userId: number
}

const getTimeDifference = (startTime: string, endTime: string) => {
  const [startHour, startMinute] = startTime.split(':')
  const [endHour, endMinute] = endTime.split(':')

  const time =
    parseInt(endHour) * 60 +
    parseInt(endMinute) -
    (parseInt(startHour) * 60 + parseInt(startMinute))

  const hours = Math.floor(time / 60)
  const minutes = time % 60

  return `${hours}h ${minutes}m`
}

export const TaskList = ({ userId }: TaskListProps) => {
  const tasks = useGetTasks(userId)

  return (
    <Box
      component="ul"
      sx={{ width: '558px', display: 'flex', flexDirection: 'column', gap: '16px' }}
    >
      {tasks.map((task) => (
        <Box
          sx={{
            backgroundColor: '#A8AEEB',
            height: '96px',
            width: '558px',
            display: 'flex',
            flexDirection: 'column',
            borderRadius: '8px',
            padding: '16px'
          }}
          key={task.id}
          component="li"
        >
          <Typography textColor="#1E2AA5">
            {task.projectName} - {task.customerName}
          </Typography>
          <Typography textColor="#1E2AA5">{task.taskType}</Typography>
          <Typography textColor="#1E2AA5" fontWeight="bold">
            {task.startTime}-{task.endTime} ({getTimeDifference(task.startTime, task.endTime)})
          </Typography>
        </Box>
      ))}
    </Box>
  )
}
