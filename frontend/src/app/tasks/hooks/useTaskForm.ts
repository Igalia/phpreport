import { useForm } from '@/hooks/useForm'
import { useTimer } from './useTimer'
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
    handleChange('startTime', format(new Date(), 'hh:mmaaa'))
    startTimer()
  }

  const onStopTimer = () => {
    handleChange('endTime', format(new Date(), 'hh:mmaaa'))
    stopTimer()
  }

  return {
    task: formState,
    handleChange,
    resetForm,
    onStartTimer,
    onStopTimer,
    seconds,
    minutes,
    hours,
    isTimerRunning
  }
}
