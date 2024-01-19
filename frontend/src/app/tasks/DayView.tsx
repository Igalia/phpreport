'use client'

import Box from '@mui/joy/Box'

import Divider from '@mui/joy/Divider'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'
import { Template } from '@/domain/Template'

import { TaskList } from './components/TaskList'
import { CreateTask } from './components/CreateTask'
import { CreateTaskFormProvider } from './providers/CreateTaskFormProvider'

type DayViewProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
  templates: Array<Template>
}

export const DayView = ({ projects, taskTypes, templates }: DayViewProps) => {
  return (
    <Box
      sx={{
        display: 'grid',
        gridTemplateAreas: {
          xs: "'divider-1 divider-1''select-template start-timer''divider-2 divider-2''create-task-form create-task-form''task-list task-list'",
          sm: "'divider-1 divider-1''select-template start-timer''divider-2 divider-2''create-task-form task-list'"
        },
        gridTemplateColumns: {
          xs: '1fr',
          sm: '558px 558px'
        },
        columnGap: '30px',
        rowGap: '16px',
        margin: '0 auto'
      }}
    >
      <Divider sx={{ gridArea: 'divider-1' }} />

      <CreateTaskFormProvider>
        <CreateTask projects={projects} taskTypes={taskTypes} templates={templates} />

        <Divider sx={{ gridArea: 'divider-2' }} />

        <TaskList sx={{ gridArea: 'task-list' }} projects={projects} taskTypes={taskTypes} />
      </CreateTaskFormProvider>
    </Box>
  )
}
