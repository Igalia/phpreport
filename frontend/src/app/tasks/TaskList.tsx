'use client'

import Box from '@mui/joy/Box'
import Button from '@mui/joy/Button'
import Typography from '@mui/joy/Typography'
import { Delete24Filled } from '@fluentui/react-icons'
import { useGetTasks, useDeleteTask } from './hooks/useTask'
import { styled } from '@mui/joy/styles'
import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'

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
  const { deleteTask } = useDeleteTask(userId)

  return (
    <Box
      component="ul"
      sx={{
        maxWidth: { xs: '100%', sm: '558px' },
        display: 'flex',
        flexDirection: 'column',
        gap: '16px',
        listStyle: 'none'
      }}
    >
      {tasks.map((task) => (
        <Box
          sx={{
            backgroundColor: '#A8AEEB',
            height: '96px',
            width: { xs: '100%', sm: '558px' },
            borderRadius: '8px',
            padding: '16px',
            display: 'flex',
            justifyContent: 'space-between'
          }}
          key={task.id}
          component="li"
        >
          <Box display="flex" flexDirection="column">
            <Typography textColor="#1E2AA5">
              {task.projectName} - {task.customerName}
            </Typography>
            <Typography textColor="#1E2AA5">{task.taskType}</Typography>
            <Typography textColor="#1E2AA5" fontWeight="bold">
              {task.startTime}-{task.endTime} ({getTimeDifference(task.startTime, task.endTime)})
            </Typography>
          </Box>
          <Box display="flex" gap="24px">
            <IconButton onClick={() => deleteTask(task.id)}>
              <Delete24Filled color="#2f3338" />
              <ScreenReaderOnly>Delete task {task.id}</ScreenReaderOnly>
            </IconButton>
          </Box>
        </Box>
      ))}
    </Box>
  )
}

const IconButton = styled(Button)`
  background: transparent;
  padding: 0;

  &:hover {
    background: transparent;
  }
`
