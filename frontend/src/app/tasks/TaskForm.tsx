'use client'

import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'
import { Select, FreeSoloSelect } from '@/ui/Select/Select'
import { Input } from '@/ui/Input/Input'
import { TextArea } from '@/ui/TextArea/TextArea'
import Typography from '@mui/joy/Typography'
import Divider from '@mui/joy/Divider'
import { Play24Filled, RecordStop24Regular } from '@fluentui/react-icons'

import { Project } from '@/domain/Project'
import { TaskType } from '@/domain/TaskType'

import { useTaskForm } from './hooks/useTaskForm'

const timeOptions = () => {
  const hours = Array.from({ length: 24 }, (_, i) => i)
  const minutes = ['00', '15', '30', '45']

  return hours.flatMap((h) => minutes.map((m) => `${h}:${m}`))
}

type TaskFormProps = {
  projects: Array<Project>
  taskTypes: Array<TaskType>
}

export const TaskForm = ({ projects, taskTypes }: TaskFormProps) => {
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
    <Stack
      onSubmit={(e) => {
        e.preventDefault()
        handleSubmit()
      }}
      component="form"
      maxWidth="558px"
      margin="0 auto"
      gap="16px"
      ref={formRef}
    >
      <Select
        onChange={(_, option) => handleChange('projectId', option?.id || '')}
        value={projects.find((project) => project.id === task.projectId) || null}
        name="projectId"
        label="Select project"
        options={projects}
        getOptionLabel={(option) => option.description}
        required
      />
      <Stack flexDirection="row" gap="30px">
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
        <FreeSoloSelect
          sx={{ width: '166px' }}
          name="startTime"
          label="From"
          value={task.startTime}
          onChange={(_, option) => {
            if (option) {
              selectStartTime(option)
            }
          }}
          options={timeOptions()}
          disabled={isTimerRunning}
          required
        />
        <FreeSoloSelect
          sx={{ width: '166px' }}
          name="endTime"
          label="To"
          value={task.endTime}
          onChange={(_, option) => {
            if (option) {
              handleChange('endTime', option)
            }
          }}
          options={timeOptions()}
          disabled={isTimerRunning}
          required
        />
      </Stack>
      <Stack bgcolor="#EFEFF4" padding="26px 16px" borderRadius="8px">
        <Typography textColor="#3D4248" fontWeight="600">
          Logged Time
        </Typography>
        <Typography textColor="#004c92" fontSize="24px" fontWeight="600">
          {loggedTime}
        </Typography>
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
        value={taskTypes.find((taskType) => taskType.slug === task.taskType) || null}
        onChange={(_, option) => handleChange('taskType', option?.slug || '')}
        options={taskTypes}
        getOptionLabel={(option) => option.name}
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
        <Select
          sx={{ width: { xs: '100%', sm: '264px' } }}
          name="moreActions"
          label="More Actions"
          options={[]}
        />
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
  )
}
