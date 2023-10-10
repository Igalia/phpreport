import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { format } from 'date-fns'
import { useEffect, useRef, useCallback } from 'react'
import { useAddTask } from './useTask'

type Task = {
  projectId: string
  taskType: string
  story: string
  description: string
  startTime: string
  endTime: string
  date: string
}

export const useTaskForm = () => {
  const formRef = useRef<HTMLFormElement>(null)

  const { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning } = useTimer()
  const { formState, handleChange, resetForm } = useForm<Task>({
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

  const { addTask } = useAddTask()

  const handleSubmit = useCallback(() => {
    console.log({ formState })
    // addTask({ token, task: formState })
  }, [formState])

  const setDate = () => handleChange('date', format(new Date(), 'yyyy-mm-dd'))

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
      event.preventDefault()
      if (event.key === 's' && event.ctrlKey) {
        formRef.current?.requestSubmit()
      }
    }
    // attach the event listener
    document.addEventListener('keydown', handleKeyPress)

    // remove the event listener
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
    formRef
  }
}
