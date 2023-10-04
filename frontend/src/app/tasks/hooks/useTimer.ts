import { useState, useEffect } from 'react'

export const useTimer = () => {
  const [time, setTime] = useState(0)
  const [isTimerRunning, setIsTimerRunning] = useState(false)

  const startTimer = () => setIsTimerRunning(true)
  const stopTimer = () => {
    setIsTimerRunning(false)
    setTime(0)
  }

  useEffect(() => {
    let intervalId: NodeJS.Timeout | undefined
    if (isTimerRunning) {
      intervalId = setInterval(() => setTime(time + 1), 1000)
    }
    return () => clearInterval(intervalId)
  }, [isTimerRunning, time])

  const seconds = Math.floor(time % 60)
  const minutes = Math.floor((time / 60) % 60)
  const hours = Math.floor((time / 3600) % 60)

  return { startTimer, stopTimer, seconds, minutes, hours, isTimerRunning }
}
