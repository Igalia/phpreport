'use client'

import Box from '@mui/joy/Box'
import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'
import { AuthorizedPage } from '@/app/auth/AuthorizedPage'
import { Select } from '@/ui/Select/Select'
import { Input } from '@/ui/Input/Input'
import { TextArea } from '@/ui/TextArea/TextArea'

import { useProjects } from './hooks/useProjects'
import { useTaskTypes } from './hooks/useTaskTypes'
import { useForm } from './hooks/useForm'

type Task = {
  project: string
  taskType: string
  story: string
  description: string
}

export default function Tasks() {
  const { projects, isLoading: isProjectsLoading } = useProjects()
  const taskTypes = useTaskTypes()

  const { formState, handleChange, resetForm } = useForm<Task>({
    initialValues: {
      project: '',
      taskType: '',
      story: '',
      description: ''
    }
  })

  return (
    <AuthorizedPage>
      <Stack
        onSubmit={(e) => {
          e.preventDefault()
          console.log(formState)
        }}
        component="form"
        maxWidth="558px"
        margin="0 auto"
        gap="16px"
      >
        <Select
          onChange={(_, option) => option && handleChange('project', option.value)}
          value={formState.project}
          name="project"
          label="Select project"
          options={projects.map((project) => ({ value: project.id, label: project.description }))}
          loading={isProjectsLoading}
        />
        <Select
          name="taskType"
          label="Select task type"
          value={formState.taskType}
          onChange={(_, option) => option && handleChange('taskType', option.value)}
          options={taskTypes.map((taskType) => ({ value: taskType.slug, label: taskType.name }))}
        />
        <Input
          value={formState.story}
          onChange={(e) => handleChange('story', e.target.value)}
          name="story"
          placeholder="Story"
          label="Story"
        />
        <Box>Logged Time 0h 0m 0s</Box>
        <Stack flexDirection="row" gap="30px">
          <Box>Start timer</Box>
          <Box>From</Box>
          <Box>To</Box>
        </Stack>
        <TextArea
          name="description"
          onChange={(e) => handleChange('description', e.target.value)}
          label="Task description"
          sx={{ minHeight: '208px' }}
          placeholder="Task description..."
          value={formState.description}
        ></TextArea>
        <Stack flexDirection="row">
          <Select sx={{ width: '264px' }} name="moreActions" label="More Actions" options={[]} />
          <Button
            onClick={resetForm}
            sx={{ ml: 'auto', mr: '20px', width: '82px', height: '40px' }}
          >
            Clear
          </Button>
          <Button sx={{ width: '82px', height: '40px' }} type="submit">
            Save
          </Button>
        </Stack>
      </Stack>
    </AuthorizedPage>
  )
}
