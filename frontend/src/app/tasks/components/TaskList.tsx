'use client'

import { Box, Skeleton } from '@mui/joy'

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
  const { tasks, isLoading } = useGetTasks()

  return (
    <Box
      component="ul"
      sx={{
        display: 'flex',
        flexDirection: 'column',
        gap: '16px',
        listStyle: 'none',
        position: 'relative',
        ...sx
      }}
    >
      <Skeleton width="100%" height="96px" loading={isLoading} />
      {tasks.map((task) => (
        <TaskBox projects={projects} taskTypes={taskTypes} key={task.id} task={task} />
      ))}
    </Box>
  )
}
