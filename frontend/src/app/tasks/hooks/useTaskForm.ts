import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { format } from 'date-fns'
import { useEffect, useRef, useCallback } from 'react'
import { useAddTask } from './useTask'
import { TaskIntent } from '@/domain/Task'

export const useTaskForm = ({ userId }: { userId: TaskIntent['userId'] }) => {
  const formRef = useRef<HTMLFormElement>(null)
  const { addTask } = useAddTask()

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
    addTask(formState)
  }, [addTask, formState])

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
    formRef
  }
}
