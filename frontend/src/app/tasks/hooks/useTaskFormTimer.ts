import { format } from 'date-fns'
import { useCallback, useEffect } from 'react'
import { TaskIntent } from '@/domain/Task'
import { useForm } from '@/hooks/useForm/useForm'
import { useTimer } from '@/hooks/useTimer/useTimer'
import { convertTimeToMinutes, getTimeDifference } from '../utils/time'

type UseTaskFormTimerProps = {
  handleChange: ReturnType<typeof useForm<TaskIntent>>['handleChange']
  startTime: TaskIntent['startTime']
  endTime: TaskIntent['endTime']
}

export const useTaskFormTimer = ({ handleChange, startTime, endTime }: UseTaskFormTimerProps) => {
  const {
    startTimer,
    stopTimer,
    seconds,
    minutes,
    hours,
    isTimerRunning,
    startTime: initialTimerValue
  } = useTimer()

  useEffect(() => {
    if (isTimerRunning) {
      handleChange('startTime', format(initialTimerValue, 'HH:mm'))
      handleChange('endTime', '')
    }
  }, [handleChange, initialTimerValue, isTimerRunning])

  const toggleTimer = useCallback(() => {
    const onStopTimer = () => {
      handleChange('endTime', format(new Date(), 'HH:mm'))
      stopTimer()
    }

    return isTimerRunning ? onStopTimer() : startTimer()
  }, [handleChange, isTimerRunning, startTimer, stopTimer])

  const loggedTime = useCallback(() => {
    if (isTimerRunning) {
      return `${hours}h ${minutes}m ${seconds}s`
    }

    const startMinutes = convertTimeToMinutes(startTime)
    const endMinutes = convertTimeToMinutes(endTime)

    if (endMinutes - startMinutes <= 0 || Number.isNaN(endMinutes) || Number.isNaN(startMinutes)) {
      return '0h 0m 0s'
    }

    return `${getTimeDifference(startMinutes, endMinutes)} 0s`
  }, [endTime, hours, isTimerRunning, minutes, seconds, startTime])

  return {
    loggedTime: loggedTime(),
    toggleTimer,
    isTimerRunning
  }
}
