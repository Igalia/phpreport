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
  tasks: Array<Task>
  projects: Array<Project>
  taskTypes: Array<TaskType>
  closeForm: () => void
}

export const EditTask = ({ task, tasks, projects, taskTypes, closeForm }: EditTaskProps) => {
  const { handleChange, handleProject, formState, handleSubmit, resetForm } = useEditTaskForm({
    task,
    tasks,
    closeForm
  })

  return (
    <Stack
      onSubmit={(e) => {
        e.preventDefault()
        handleSubmit()
      }}
      component="form"
      gap="8px"
      width="400px"
    >
      <Select
        onChange={(value) => handleProject(value, projects)}
        value={formState.projectName}
        name="projectId"
        label="Select project"
        placeholder="Select project"
        options={projects.map((project) => project.description)}
        required
      />
      <Select
        name="taskType"
        label="Select task type"
        value={formState.taskType || ''}
        onChange={(value) => handleChange('taskType', value)}
        options={taskTypes.map((taskType) => taskType.slug)}
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
