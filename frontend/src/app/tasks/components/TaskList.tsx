'use client'

import Box from '@mui/joy/Box'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'

import { useGetTasks } from '../hooks/useGetTasks'
import { TaskBox } from './TaskBox'
import { SxProps } from '@mui/joy/styles/types'

type TaskListProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
  sx?: SxProps
}

export const TaskList = ({ projects, taskTypes, sx }: TaskListProps) => {
  const { tasks } = useGetTasks()

  return (
    <Box
      component="ul"
      sx={{
        display: 'flex',
        flexDirection: 'column',
        gap: '16px',
        listStyle: 'none',
        ...sx
      }}
    >
      {tasks.map((task) => (
        <TaskBox projects={projects} taskTypes={taskTypes} key={task.id} task={task} />
      ))}
    </Box>
  )
}
