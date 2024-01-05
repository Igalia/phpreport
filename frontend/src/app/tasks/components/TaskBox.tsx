'use client'
import { useState } from 'react'

import Box from '@mui/joy/Box'
import Button from '@mui/joy/Button'
import Typography from '@mui/joy/Typography'
import {
  Delete24Filled,
  Edit24Filled,
  ArrowMaximize24Filled,
  ArrowMinimize24Filled,
  SaveCopy24Filled,
  Copy24Filled
} from '@fluentui/react-icons'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'
import { Task } from '@/domain/Task'

import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'
import { ConfirmationModal } from '@/ui/ConfirmationModal/ConfirmationModal'
import { styled } from '@mui/joy/styles'
import { getTimeDifference, convertTimeToMinutes } from '../utils/time'

import { EditTask } from './EditTask'
import { useDeleteTask } from '../hooks/useDeleteTask'
import { SaveTemplateModal } from './SaveTemplateModal'
import { useCreateTaskForm } from '../hooks/useCreateTaskForm'

type TaskProps = {
  task: Task
  projects: Array<Project>
  taskTypes: Array<TaskType>
}

export const TaskBox = ({ task, projects, taskTypes }: TaskProps) => {
  const { deleteTask } = useDeleteTask()
  const { cloneTask } = useCreateTaskForm()

  const [deleteModalOpen, setDeleteModalOpen] = useState(false)
  const [templateModalOpen, setTemplateModalOpen] = useState(false)
  const [editMode, setEditMode] = useState(false)
  const [expandedTask, setExpandedTask] = useState(false)

  const timeDifference = () => {
    const startMinutes = convertTimeToMinutes(task.startTime)
    const endMinutes = convertTimeToMinutes(task.endTime)

    return getTimeDifference(startMinutes, endMinutes)
  }

  return (
    <Box
      sx={{
        backgroundColor: '#A8AEEB',
        minHeight: '96px',
        borderRadius: '8px',
        padding: '16px',
        display: 'flex',
        justifyContent: 'space-between'
      }}
      component="li"
    >
      <Box display="flex" flexDirection="column" gap={editMode ? '8px' : 0}>
        {editMode ? (
          <EditTask
            closeForm={() => setEditMode(false)}
            task={task}
            projects={projects}
            taskTypes={taskTypes}
          />
        ) : (
          <>
            <Typography textColor="#1E2AA5">
              {task.projectName} - {task.customerName}
            </Typography>
            <Typography textColor="#1E2AA5">{task.taskType}</Typography>
            <Typography textColor="#1E2AA5" fontWeight="bold">
              {task.startTime}-{task.endTime} ({timeDifference()})
            </Typography>
            {expandedTask && (
              <>
                <Typography textColor="#1E2AA5">{task.description}</Typography>
                <Typography textColor="#1E2AA5">{task.story}</Typography>
              </>
            )}
          </>
        )}
      </Box>
      <Box display="flex" gap="8px" flexDirection={editMode ? 'column' : 'row'}>
        <IconButton
          onClick={() => {
            setEditMode(true)
            setExpandedTask(false)
          }}
        >
          <Edit24Filled color="#2f3338" />
          <ScreenReaderOnly>Edit task</ScreenReaderOnly>
        </IconButton>
        <IconButton onClick={() => setDeleteModalOpen(true)}>
          <Delete24Filled color="#2f3338" />
          <ScreenReaderOnly>Delete task {task.id}</ScreenReaderOnly>
        </IconButton>
        <IconButton onClick={() => setTemplateModalOpen(true)}>
          <SaveCopy24Filled color="#2f3338" />
          <ScreenReaderOnly>Save task as template</ScreenReaderOnly>
        </IconButton>
        <IconButton onClick={() => cloneTask(task)}>
          <Copy24Filled color="#2f3338" />
          <ScreenReaderOnly>Clone Task</ScreenReaderOnly>
        </IconButton>
        <IconButton onClick={() => setExpandedTask((prevState) => !prevState)}>
          {expandedTask ? (
            <ArrowMinimize24Filled color="#2f3338" />
          ) : (
            <ArrowMaximize24Filled color="#2f3338" />
          )}
          <ScreenReaderOnly>Expand Task</ScreenReaderOnly>
        </IconButton>
        <SaveTemplateModal
          task={task}
          open={templateModalOpen}
          closeModal={() => setTemplateModalOpen(false)}
        />
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
  height: 24px;
  width: 24px;

  &:hover {
    background: transparent;
  }
`
