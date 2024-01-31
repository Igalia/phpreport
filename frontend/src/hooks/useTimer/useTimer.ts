import { useState, useEffect, useCallback, useMemo } from 'react'

export const useTimer = () => {
  const [time, setTime] = useState(0)
  const [startTime, setStartTime] = useState(Number(localStorage.getItem('timer_start')) || 0)

  const startTimer = useCallback(() => {
    localStorage.setItem('timer_start', Date.now().toString())
    setStartTime(Date.now())
  }, [])

  const stopTimer = useCallback(() => {
    localStorage.removeItem('timer_start')
    setStartTime(0)
    setTime(0)
  }, [])

  const isTimerRunning = useMemo(() => startTime > 0, [startTime])

  useEffect(() => {
    let intervalId: NodeJS.Timeout | undefined
    if (isTimerRunning) {
      intervalId = setInterval(() => setTime((Date.now() - startTime) / 1000), 1000)
    }
    return () => clearInterval(intervalId)
  }, [isTimerRunning, startTime])

  const seconds = Math.floor(time % 60)
  const minutes = Math.floor((time / 60) % 60)
  const hours = Math.floor((time / 3600) % 60)

  return { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning, startTime }
}
