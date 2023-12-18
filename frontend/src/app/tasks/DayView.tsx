'use client'

import Box from '@mui/joy/Box'

import Divider from '@mui/joy/Divider'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'
import { Template } from '@/domain/Template'

import { TaskList } from './components/TaskList'
import { CreateTask } from './components/CreateTask'

type DayViewProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
  templates: Array<Template>
}

export const DayView = ({ projects, taskTypes, templates }: DayViewProps) => {
  return (
    <Box
      sx={{
        padding: { xs: '0 8px', sm: '0 32px' },
        display: 'grid',
        gridTemplateAreas: {
          xs: "'select-template start-timer''divider divider''create-task-form create-task-form''task-list task-list'",
          sm: "'select-template start-timer''divider divider''create-task-form task-list'"
        },
        gridTemplateColumns: {
          xs: '1fr',
          sm: '558px 558px'
        },
        margin: '0 auto',
        maxWidth: '1146px',
        columnGap: '30px',
        rowGap: '16px'
      }}
    >
      <CreateTask projects={projects} taskTypes={taskTypes} templates={templates} />

      <Divider sx={{ gridArea: 'divider' }} />

      <TaskList sx={{ gridArea: 'task-list' }} projects={projects} taskTypes={taskTypes} />
    </Box>
  )
}
