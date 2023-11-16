'use client'

import Box from '@mui/joy/Box'

import { useGetTasks, useDeleteTask } from './hooks/useTask'
import { TaskBox } from './components/Task'

type TaskListProps = {
  userId: number
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
        <TaskBox key={task.id} task={task} deleteTask={deleteTask} />
      ))}
    </Box>
  )
}
