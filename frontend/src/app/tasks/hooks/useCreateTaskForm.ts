import { format } from 'date-fns'
import { useEffect, useRef, useCallback, useState } from 'react'

import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'

import { useGetTasks } from './useGetTasks'
import { useCreateTask } from './useCreateTask'

import { useAlert } from '@/ui/Alert/useAlert'
import { Template } from '@/domain/Template'
import { Project } from '@/domain/Project'
import { TaskIntent, getOverlappingTasks } from '@/domain/Task'
import { convertTimeToMinutes, getTimeDifference } from '../utils/time'

export const useTaskForm = () => {
  const formRef = useRef<HTMLFormElement>(null)
  const { id: userId } = useGetCurrentUser()
  const { addTask } = useCreateTask()
  const { showError } = useAlert()
  const { tasks } = useGetTasks()
  const [templateName, setTemplateName] = useState('')

  const { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning } = useTimer()
  const { formState, handleChange, resetForm, setFormState } = useForm<TaskIntent>({
    initialValues: {
      userId,
      projectId: null,
      projectName: '',
      taskType: '',
      story: '',
      description: '',
      startTime: '',
      endTime: '',
      date: format(new Date(), 'yyyy-MM-dd')
    }
  })

  const handleSubmit = useCallback(() => {
    const validation = TaskIntent.safeParse(formState)

    if (!validation.success) {
      validation.error.issues.map(({ message }) => {
        showError(message)
      })

      return
    }

    const { message } = getOverlappingTasks(formState, tasks)

    if (message.length > 0) {
      showError(message)
      return
    }

    addTask(formState)
  }, [addTask, formState, showError, tasks])

  const handleProject = (value: string, projects: Array<Project>) => {
    const project = projects.find((project) => project.description === value)
    handleChange('projectName', value)
    if (project) {
      handleChange('projectId', project.id)
    } else {
      handleChange('projectId', null)
    }
  }

  const selectTemplate = (
    templateId: number,
    templates: Array<Template>,
    projects: Array<Project>
  ) => {
    const template = templates.find((t) => t.id === templateId)
    if (template) {
      const project = projects.find((project) => project.id === template.projectId)
      setTemplateName(template.name)
      setFormState((prevState) => ({
        ...prevState,
        projectName: project?.description || '',
        taskType: template.taskType,
        description: template.description || '',
        startTime: template.startTime || '',
        endTime: template.endTime || '',
        projectId: template.projectId,
        story: template.story || ''
      }))
    }
  }

  const onStartTimer = () => {
    handleChange('startTime', format(new Date(), 'HH:mm'))
    handleChange('endTime', '')

    startTimer()
  }

  const selectStartTime = (option: string) => {
    handleChange('startTime', option)
  }

  const onStopTimer = () => {
    handleChange('endTime', format(new Date(), 'HH:mm'))
    stopTimer()
  }

  useEffect(() => {
    const handleKeyPress = (event: KeyboardEvent) => {
      if (event.key === 's' && event.ctrlKey && formRef.current?.contains(document.activeElement)) {
        event.preventDefault()
        formRef.current?.requestSubmit()
      }
    }
    document.addEventListener('keydown', handleKeyPress)

    return () => {
      document.removeEventListener('keydown', handleKeyPress)
    }
  }, [handleSubmit])

  const toggleTimer = () => (isTimerRunning ? onStopTimer() : onStartTimer())

  const makeLoggedTime = () => {
    if (isTimerRunning) {
      return `${hours}h ${minutes}m ${seconds}s`
    }

    const startMinutes = convertTimeToMinutes(formState.startTime)
    const endMinutes = convertTimeToMinutes(formState.endTime)

    if (endMinutes - startMinutes <= 0 || Number.isNaN(endMinutes) || Number.isNaN(startMinutes)) {
      return '0h 0m 0s'
    }

    return `${getTimeDifference(startMinutes, endMinutes)} 0s`
  }

  return {
    task: formState,
    handleChange,
    resetForm,
    toggleTimer,
    loggedTime: makeLoggedTime(),
    isTimerRunning,
    selectStartTime,
    handleProject,
    handleSubmit,
    formRef,
    selectTemplate,
    templateName
  }
}
