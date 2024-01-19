'use client'
import { useState } from 'react'

import Box from '@mui/joy/Box'
import Typography from '@mui/joy/Typography'
import {
  Edit24Filled,
  ArrowMaximize24Filled,
  ArrowMinimize24Filled,
  Copy24Filled
} from '@fluentui/react-icons'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'
import { Task } from '@/domain/Task'

import { getTimeDifference, convertTimeToMinutes } from '../utils/time'

import { EditTask } from './EditTask'
import { DeleteTask } from './DeleteTask'
import { SaveTemplateModal } from './SaveTemplateModal'
import { useCreateTaskForm } from '../hooks/useCreateTaskForm'
import { IconButton } from './IconButton'
import { styled } from '@mui/joy'

type TaskBoxProps = {
  task: Task
  projects: Array<Project>
  taskTypes: Array<TaskType>
}

type SimpleTaskBoxProps = {
  task: Task
}

const timeDifference = ({ startTime, endTime }: { startTime: string; endTime: string }) => {
  const startMinutes = convertTimeToMinutes(startTime)
  const endMinutes = convertTimeToMinutes(endTime)

  return getTimeDifference(startMinutes, endMinutes)
}

export const SimpleTaskBox = ({ task }: SimpleTaskBoxProps) => {
  return (
    <TaskWrapper
      sx={{ maxWidth: { xs: '250px', sm: 'auto' } }}
      width="100%"
      display="flex"
      flexDirection="column"
    >
      <Typography
        sx={{ textOverflow: 'ellipsis', overflow: 'hidden', whiteSpace: 'nowrap' }}
        textColor="#1E2AA5"
      >
        {task.projectName} - {task.customerName}
      </Typography>
      <Typography textColor="#1E2AA5" sx={{ display: { xs: 'none', sm: 'block' } }}>
        {task.taskType}
      </Typography>
      <Typography textColor="#1E2AA5" fontWeight="bold">
        {task.startTime}-{task.endTime} ({timeDifference(task)})
      </Typography>
    </TaskWrapper>
  )
}

export const TaskBox = ({ task, projects, taskTypes }: TaskBoxProps) => {
  const { cloneTask } = useCreateTaskForm()

  const [editMode, setEditMode] = useState(false)
  const [expandedTask, setExpandedTask] = useState(false)

  return (
    <TaskWrapper
      sx={{
        minHeight: '96px'
      }}
      component="li"
    >
      {editMode ? (
        <EditTask
          closeForm={() => setEditMode(false)}
          task={task}
          projects={projects}
          taskTypes={taskTypes}
        />
      ) : (
        <Box display="flex" flexDirection="column">
          <Typography textColor="#1E2AA5">
            {task.projectName} - {task.customerName}
          </Typography>
          <Typography textColor="#1E2AA5">{task.taskType}</Typography>
          <Typography textColor="#1E2AA5" fontWeight="bold">
            {task.startTime}-{task.endTime} ({timeDifference(task)})
          </Typography>
          {expandedTask && (
            <>
              <Typography textColor="#1E2AA5">{task.description}</Typography>
              <Typography textColor="#1E2AA5">{task.story}</Typography>
            </>
          )}
        </Box>
      )}
      <Box display="flex" gap="8px" flexDirection={editMode ? 'column' : 'row'}>
        <IconButton
          onClick={() => {
            setEditMode(true)
          }}
          icon={Edit24Filled}
          description="Edit task"
        />
        <IconButton description="Clone Task" icon={Copy24Filled} onClick={() => cloneTask(task)} />
        <DeleteTask taskId={task.id} />
        <SaveTemplateModal task={task} />
        <IconButton
          description="Expand Task"
          icon={expandedTask ? ArrowMinimize24Filled : ArrowMaximize24Filled}
          onClick={() => setExpandedTask((prevState) => !prevState)}
        />
      </Box>
    </TaskWrapper>
  )
}

const TaskWrapper = styled(Box)`
  background-color: #a8aeeb;
  border-radius: 8px;
  padding: 16px;
  display: flex;
  justify-content: space-between;
`
