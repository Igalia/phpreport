'use client'
import { useState } from 'react'

import Box from '@mui/joy/Box'
import Button from '@mui/joy/Button'
import Typography from '@mui/joy/Typography'
import { Delete24Filled } from '@fluentui/react-icons'

import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'
import { ConfirmationModal } from '@/ui/ConfirmationModal/ConfirmationModal'
import { styled } from '@mui/joy/styles'
import { Task } from '@/domain/Task'
import { getTimeDifference, convertTimeToMinutes } from '../utils/time'

type TaskProps = {
  task: Task
  deleteTask: (taskId: number) => void
}

export const TaskBox = ({ task, deleteTask }: TaskProps) => {
  const [deleteModalOpen, setDeleteModalOpen] = useState(false)

  const timeDifference = () => {
    const startMinutes = convertTimeToMinutes(task.startTime)
    const endMinutes = convertTimeToMinutes(task.endTime)

    return getTimeDifference(startMinutes, endMinutes)
  }

  return (
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
      component="li"
    >
      <Box display="flex" flexDirection="column">
        <Typography textColor="#1E2AA5">
          {task.projectName} - {task.customerName}
        </Typography>
        <Typography textColor="#1E2AA5">{task.taskType}</Typography>
        <Typography textColor="#1E2AA5" fontWeight="bold">
          {task.startTime}-{task.endTime} ({timeDifference()})
        </Typography>
      </Box>
      <Box display="flex" gap="24px">
        <IconButton onClick={() => setDeleteModalOpen(true)}>
          <Delete24Filled color="#2f3338" />
          <ScreenReaderOnly>Delete task {task.id}</ScreenReaderOnly>
        </IconButton>
        <ConfirmationModal
          open={deleteModalOpen}
          closeModal={() => setDeleteModalOpen(false)}
          confirmAction={() => {
            setDeleteModalOpen(false)
            deleteTask(task.id)
          }}
          title="Confirm Deletion"
          content="This task will be deleted"
          confirmText="Delete"
        />
      </Box>
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
