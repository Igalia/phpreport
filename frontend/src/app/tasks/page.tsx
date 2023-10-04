'use client'
import Stack from '@mui/joy/Stack'
import Button from '@mui/joy/Button'
import { AuthorizedPage } from '@/app/auth/AuthorizedPage'
import { Select, FreeSoloSelect } from '@/ui/Select/Select'
import { Input } from '@/ui/Input/Input'
import { TextArea } from '@/ui/TextArea/TextArea'
import Typography from '@mui/joy/Typography'
import Divider from '@mui/joy/Divider'
import { Play24Filled, RecordStop24Regular } from '@fluentui/react-icons'

import { useProjects } from './hooks/useProjects'
import { useTaskTypes } from './hooks/useTaskTypes'
import { useTaskForm } from './hooks/useTaskForm'

const timeOptions = () => {
  const hours = Array.from({ length: 12 }, (_, i) => i + 1)
  hours.unshift(hours.pop()!)
  const minutes = ['00', '15', '30', '45']
  const meridians = ['am', 'pm']

  const time = hours.flatMap((h) => minutes.map((m) => `${h}:${m}`))

  return meridians.flatMap((meridian) => time.map((t) => `${t}${meridian}`))
}

export default function Tasks() {
  const { projects, isLoading: isProjectsLoading } = useProjects()
  const taskTypes = useTaskTypes()
  const {
    task,
    handleChange,
    resetForm,
    onStartTimer,
    onStopTimer,
    seconds,
    minutes,
    hours,
    isTimerRunning
  } = useTaskForm()

  return (
    <AuthorizedPage>
      <Stack
        onSubmit={(e) => {
          e.preventDefault()
          console.log(task)
        }}
        component="form"
        maxWidth="558px"
        margin="0 auto"
        gap="16px"
      >
        <Select
          onChange={(_, option) => handleChange('projectId', option?.id || '')}
          value={projects.find((project) => project.id === task.projectId)}
          name="projectId"
          label="Select project"
          options={projects}
          loading={isProjectsLoading}
          getOptionLabel={(option) => option.id}
        />
        <Stack flexDirection="row" gap="30px">
          <Button
            sx={{ width: '166px', display: 'flex', gap: '8px' }}
            onClick={() => {
              isTimerRunning ? onStopTimer() : onStartTimer()
            }}
          >
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
                handleChange('startTime', option)
              }
            }}
            options={timeOptions()}
            disabled={isTimerRunning}
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
          />
        </Stack>
        <Stack bgcolor="#EFEFF4" padding="26px 16px" borderRadius="8px">
          <Typography textColor="#3D4248" fontWeight="600">
            Logged Time
          </Typography>
          <Typography textColor="#004c92" fontSize="24px" fontWeight="600">
            {hours}h {minutes}m {seconds}s
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
          value={taskTypes.find((taskType) => taskType.slug === task.taskType)}
          onChange={(_, option) => handleChange('taskType', option?.slug || '')}
          options={taskTypes}
          getOptionLabel={(option) => option.slug}
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
    </AuthorizedPage>
  )
}
