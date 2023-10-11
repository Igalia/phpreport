import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { format } from 'date-fns'
import { useEffect, useRef, useCallback, useState } from 'react'
import { useAddTask } from './useTask'
import { useCurrentUser } from '@/app/user/hooks/useCurrentUser'
import { TaskIntent } from '@/domain/Task'

type Alert = {
  showAlert: boolean
  message: string
  type?: 'warning' | 'success'
}

export const useTaskForm = () => {
  const formRef = useRef<HTMLFormElement>(null)
  const { user } = useCurrentUser()
  const { addTask } = useAddTask()
  const [submitAlert, setSubmitAlert] = useState<Alert>({
    showAlert: false,
    message: 'test'
  })

  const { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning } = useTimer()
  const { formState, handleChange, resetForm } = useForm<TaskIntent>({
    initialValues: {
      projectId: '',
      taskType: '',
      story: '',
      description: '',
      startTime: '',
      endTime: '',
      date: ''
    }
  })

  useEffect(() => {
    handleChange('userId', user.id)
  }, [handleChange, user])

  const closeAlert = () =>
    setSubmitAlert({
      showAlert: false,
      message: ''
    })

  const handleSubmit = useCallback(() => {
    addTask(formState, {
      onSuccess: () => {
        setSubmitAlert({ showAlert: true, message: 'Task successfully added', type: 'success' })
      },
      onError: () => {
        setSubmitAlert({ showAlert: true, message: 'Failed to add task', type: 'warning' })
      }
    })
  }, [addTask, formState])

  useEffect(() => {
    let timeoutId: NodeJS.Timeout | undefined
    if (submitAlert.showAlert) {
      timeoutId = setTimeout(closeAlert, 15000)
    }

    return () => clearTimeout(timeoutId)
  }, [submitAlert])

  const setDate = () => handleChange('date', format(new Date(), 'yyyy-MM-dd'))

  const onStartTimer = () => {
    handleChange('startTime', format(new Date(), 'HH:mm'))
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
  const loggedTime = `${hours}h ${minutes}m ${seconds}s`

  return {
    task: formState,
    handleChange,
    resetForm,
    toggleTimer,
    loggedTime,
    isTimerRunning,
    selectStartTime,
    handleSubmit,
    formRef,
    submitAlert,
    closeAlert
  }
}
