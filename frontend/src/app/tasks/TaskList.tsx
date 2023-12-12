'use client'

import Box from '@mui/joy/Box'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'

import { useGetTasks } from './hooks/useTask'
import { TaskBox } from './components/TaskBox'

type TaskListProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
}

export const TaskList = ({ projects, taskTypes }: TaskListProps) => {
  const tasks = useGetTasks()

  return (
    <Box
      component="ul"
      sx={{
        width: '100%',
        maxWidth: { xs: '100%', sm: '558px' },
        display: 'flex',
        flexDirection: 'column',
        gap: '16px',
        listStyle: 'none'
      }}
    >
      {tasks.map((task) => (
        <TaskBox
          projects={projects}
          taskTypes={taskTypes}
          key={task.id}
          task={task}
          tasks={tasks}
        />
      ))}
    </Box>
  )
}
