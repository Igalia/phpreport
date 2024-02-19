'use client'

import Box from '@mui/joy/Box'

import Divider from '@mui/joy/Divider'
import { Skeleton } from '@mui/joy'

import { CreateTask } from '../../components/CreateTask'
import { CreateTaskFormProvider } from './providers/CreateTaskFormProvider'

export default function Loading() {
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
        margin: '0 auto',
        maxWidth: '1146px',
        width: '100%'
      }}
    >
      <Divider sx={{ gridArea: 'divider-1' }} />

      <CreateTaskFormProvider>
        <CreateTask projects={[]} taskTypes={[]} templates={[]} />

        <Divider sx={{ gridArea: 'divider-2' }} />

        <Box sx={{ gridArea: 'task-list', position: 'relative' }}>
          <Skeleton width="100%" height="96px" />
        </Box>
      </CreateTaskFormProvider>
    </Box>
  )
}
