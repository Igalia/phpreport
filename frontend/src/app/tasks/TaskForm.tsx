'use client'

import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'
import Box from '@mui/joy/Box'
import { Select } from '@/ui/Select/Select'
import { Input } from '@/ui/Input/Input'
import { TextArea } from '@/ui/TextArea/TextArea'
import Typography from '@mui/joy/Typography'
import Divider from '@mui/joy/Divider'
import { Play24Filled, RecordStop24Regular } from '@fluentui/react-icons'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'

import { useTaskForm } from './hooks/useTaskForm'
import { TimePicker } from './components/TimePicker'
import { Template } from '@/domain/Template'

type TaskFormProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
  templates: Array<Template>
  taskList: React.ReactNode
}

export const TaskForm = ({ projects, taskTypes, templates, taskList }: TaskFormProps) => {
  const {
    task,
    handleChange,
    resetForm,
    toggleTimer,
    loggedTime,
    isTimerRunning,
    selectStartTime,
    handleSubmit,
    formRef
  } = useTaskForm()

  return (
    <Stack sx={{ padding: { xs: '0 8px', sm: '0 32px' }, margin: '0 auto' }}>
      <Box
        sx={{
          display: 'flex',
          margin: '0 auto',
          gap: '30px',
          maxWidth: '1146px',
          width: '100%'
        }}
      >
        <Select
          name="templates"
          label="Select template"
          value=""
          onChange={() => {}}
          options={templates.map((template) => ({
            value: template.id.toString(),
            label: template.name
          }))}
          placeholder="Select template"
          sx={{ width: '558px' }}
        />
        <Button sx={{ width: '166px', display: 'flex', gap: '8px' }} onClick={toggleTimer}>
          {isTimerRunning ? (
            <>
              <RecordStop24Regular /> Stop Timer
            </>
          ) : (
            <>
              <Play24Filled /> Start Timer
            </>
          )}
        </Button>
      </Box>

      <Divider sx={{ margin: '16px auto', maxWidth: '1146px', width: '100%' }} />

      <Box
        sx={{
          display: 'flex',
          flexDirection: { xs: 'column', sm: 'row' },
          margin: '0 auto',
          gap: '30px',
          justifyContent: 'center',
          maxWidth: '1146px',
          width: '100%'
        }}
      >
        <Stack
          onSubmit={(e) => {
            e.preventDefault()
            handleSubmit()
          }}
          component="form"
          sx={{ maxWidth: { xs: '100%', sm: '558px' } }}
          gap="16px"
          ref={formRef}
        >
          <Select
            onChange={(value) => handleChange('projectId', value)}
            value={task.projectId}
            name="projectId"
            label="Select project"
            placeholder="Select project"
            options={projects.map((project) => ({
              label: project.description,
              value: project.id.toString()
            }))}
            required
          />
          <Stack flexDirection="row" gap="30px">
            <TimePicker
              name="startTime"
              label="From"
              value={task.startTime}
              onChange={selectStartTime}
              sx={{ width: '166px' }}
              disabled={isTimerRunning}
              required
            />

            <TimePicker
              name="endTime"
              label="To"
              value={task.endTime}
              onChange={(option: string) => {
                handleChange('endTime', option)
              }}
              sx={{ width: '166px' }}
              disabled={isTimerRunning}
              required
            />

            <Stack bgcolor="#EFEFF4" width="166px" padding="4px 16px" borderRadius="8px">
              <Typography textColor="#3D4248" fontWeight="600">
                Logged Time
              </Typography>
              <Typography textColor="#004c92" fontWeight="600">
                {loggedTime}
              </Typography>
            </Stack>
          </Stack>

          <TextArea
            name="description"
            onChange={(e) => handleChange('description', e.target.value)}
            label="Task description"
            sx={{ minHeight: '208px' }}
            placeholder="Task description..."
            value={task.description}
          ></TextArea>
          <Select
            name="taskType"
            label="Select task type"
            value={task.taskType}
            onChange={(value) => handleChange('taskType', value)}
            options={taskTypes.map((taskType) => ({ label: taskType.name, value: taskType.slug }))}
            placeholder="Select task type"
          />
          <Input
            value={task.story}
            onChange={(e) => handleChange('story', e.target.value)}
            name="story"
            placeholder="Story"
            label="Story"
          />
          <Divider />
          <Stack sx={{ flexDirection: { xs: 'column', sm: 'row' }, gap: '16px' }}>
            <Stack
              sx={{
                alignSelf: 'flex-end',
                flexDirection: 'row',
                ml: { xs: '0', sm: 'auto' },
                gap: '20px'
              }}
            >
              <Button variant="outlined" onClick={resetForm} sx={{ width: '82px' }}>
                Clear
              </Button>
              <Button sx={{ width: '82px' }} type="submit">
                Save
              </Button>
            </Stack>
          </Stack>
        </Stack>
        {taskList}
      </Box>
    </Stack>
  )
}
