import Box from '@mui/joy/Box'
import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'

import { Task } from '@/domain/Task'
import { TaskType } from '@/domain/TaskType'
import { Project } from '@/domain/Project'

import { Select } from '@/ui/Select/Select'
import { TextArea } from '@/ui/TextArea/TextArea'
import { Input } from '@/ui/Input/Input'

import { TimePicker } from './TimePicker'
import { useEditTaskForm } from '../hooks/useEditTaskForm'

type EditTaskProps = {
  task: Task
  projects: Array<Project>
  taskTypes: Array<TaskType>
  closeForm: () => void
}

export const EditTask = ({ task, projects, taskTypes, closeForm }: EditTaskProps) => {
  const { handleChange, formState, handleSubmit, resetForm, formRef } = useEditTaskForm({
    task,
    closeForm
  })

  return (
    <Stack
      onSubmit={(e) => {
        e.preventDefault()
        handleSubmit()
      }}
      ref={formRef}
      component="form"
      gap="8px"
      width="400px"
    >
      <Select
        onChange={(_, newValue) => {
          handleChange('projectId', newValue?.id || null)
        }}
        value={projects.find(({ id }) => id === formState.projectId) || null}
        name="projectId"
        label="Select project"
        placeholder="Select project"
        options={projects}
        getOptionLabel={(project) => project.description}
        required
      />
      <Select
        name="taskType"
        label="Select task type"
        value={taskTypes.find(({ slug }) => slug === formState.taskType) || null}
        onChange={(_, newValue) => handleChange('taskType', newValue?.slug || null)}
        options={taskTypes}
        getOptionLabel={(taskType) => taskType.name}
        placeholder="Select task type"
      />
      <Box display="flex" gap="8px">
        <TimePicker
          name="startTime"
          label="From"
          sx={{ width: '196px' }}
          value={formState.startTime}
          onChange={(value) => handleChange('startTime', value)}
          required
        />

        <TimePicker
          name="endTime"
          label="To"
          sx={{ width: '196px' }}
          value={formState.endTime}
          onChange={(value) => handleChange('endTime', value)}
          required
        />
      </Box>
      <TextArea
        name="description"
        onChange={(e) => handleChange('description', e.target.value)}
        label="Task description"
        placeholder="Task description..."
        value={formState.description || ''}
      />
      <Input
        value={formState.story || ''}
        onChange={(e) => handleChange('story', e.target.value)}
        name="story"
        placeholder="Story"
        label="Story"
      />
      <Box display="flex" justifyContent="flex-end" gap="8px">
        <Button
          sx={{ padding: '4px 8px', backgroundColor: 'white' }}
          onClick={() => {
            resetForm()
            closeForm()
          }}
          variant="outlined"
        >
          Close Form
        </Button>
        <Button sx={{ width: '82px' }} type="submit">
          Save
        </Button>
      </Box>
    </Stack>
  )
}
