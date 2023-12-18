import { format } from 'date-fns'
import { useEffect, useRef, useCallback } from 'react'

import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'

import { useGetTasks } from './useGetTasks'
import { useCreateTask } from './useCreateTask'

import { useAlert } from '@/ui/Alert/useAlert'
import { TaskIntent, getOverlappingTasks } from '@/domain/Task'
import { convertTimeToMinutes, getTimeDifference } from '../utils/time'

export const useTaskForm = () => {
  const formRef = useRef<HTMLFormElement>(null)
  const { id: userId } = useGetCurrentUser()
  const { addTask } = useCreateTask()
  const { showError } = useAlert()
  const { tasks } = useGetTasks()

  const { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning } = useTimer()
  const { formState, handleChange, resetForm } = useForm<TaskIntent>({
    initialValues: {
      userId,
      projectId: '',
      taskType: '',
      story: '',
      description: '',
      startTime: '',
      endTime: '',
      date: ''
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

  const setDate = () => handleChange('date', format(new Date(), 'yyyy-MM-dd'))

  const onStartTimer = () => {
    handleChange('startTime', format(new Date(), 'HH:mm'))
    handleChange('endTime', '')

    setDate()
    startTimer()
  }

  const selectStartTime = (option: string) => {
    handleChange('startTime', option)
    setDate()
  }

  const onStopTimer = () => {
    handleChange('endTime', format(new Date(), 'HH:mm'))
    stopTimer()
  }

  useEffect(() => {
    const handleKeyPress = (event: KeyboardEvent) => {
      if (event.key === 's' && event.ctrlKey) {
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
    handleSubmit,
    formRef
  }
}
