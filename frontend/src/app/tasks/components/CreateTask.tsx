import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'
import { Select } from '@/ui/Select/Select'
import { Input } from '@/ui/Input/Input'
import { TextArea } from '@/ui/TextArea/TextArea'
import Typography from '@mui/joy/Typography'
import Divider from '@mui/joy/Divider'

import { Play16Filled, RecordStop24Regular } from '@fluentui/react-icons'

import { TimePicker } from '../components/TimePicker'
import { useTaskForm } from '../hooks/useTaskForm'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'
import { Template } from '@/domain/Template'

type CreateTaskProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
  templates: Array<Template>
}

export const CreateTask = ({ projects, taskTypes, templates }: CreateTaskProps) => {
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
    <>
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
        sx={{ gridArea: 'select-template' }}
      />
      <Button
        sx={{
          width: { xs: '100%', sm: '120px' },
          display: 'flex',
          gap: '8px',
          gridArea: 'start-timer'
        }}
        onClick={toggleTimer}
      >
        {isTimerRunning ? (
          <>
            <RecordStop24Regular /> Stop Timer
          </>
        ) : (
          <>
            <Play16Filled /> Start Timer
          </>
        )}
      </Button>
      <Stack
        onSubmit={(e) => {
          e.preventDefault()
          handleSubmit()
        }}
        component="form"
        sx={{ gridArea: 'create-task-form' }}
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
        <Stack flexDirection="row" sx={{ gap: { xs: '8px', sm: '30px' } }}>
          <TimePicker
            name="startTime"
            label="From"
            value={task.startTime}
            onChange={selectStartTime}
            sx={{ width: { xs: '120px', sm: '166px' } }}
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
            sx={{ width: { xs: '120px', sm: '166px' } }}
            disabled={isTimerRunning}
            required
          />

          <Stack
            bgcolor="#EFEFF4"
            sx={{ padding: '0px 16px', borderRadius: '8px', width: { xs: '100%', sm: '166px' } }}
          >
            <Typography fontSize="sm" textColor="#3D4248" fontWeight="600">
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
          value={task.taskType || ''}
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
    </>
  )
}
