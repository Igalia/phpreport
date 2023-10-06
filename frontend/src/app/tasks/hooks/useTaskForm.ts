import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { format } from 'date-fns'

type Task = {
  projectId: string
  taskType: string
  story: string
  description: string
  startTime: string
  endTime: string
}

export const useTaskForm = () => {
  const { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning } = useTimer()
  const { formState, handleChange, resetForm } = useForm<Task>({
    initialValues: {
      projectId: '',
      taskType: '',
      story: '',
      description: '',
      startTime: '',
      endTime: ''
    }
  })

  const onStartTimer = () => {
    handleChange('startTime', format(new Date(), 'HH:mm'))
    startTimer()
  }

  const onStopTimer = () => {
    handleChange('endTime', format(new Date(), 'HH:mm'))
    stopTimer()
  }

  const toggleTimer = () => (isTimerRunning ? onStopTimer() : onStartTimer())
  const loggedTime = `${hours}h ${minutes}m ${seconds}s`

  return {
    task: formState,
    handleChange,
    resetForm,
    toggleTimer,
    loggedTime,
    isTimerRunning
  }
}
