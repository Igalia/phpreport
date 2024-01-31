import { renderHook, act } from '@testing-library/react'
import { useTimer } from '../useTimer'

describe('useTimer', () => {
  beforeAll(() => {
    jest.useFakeTimers()
    jest.spyOn(global, 'setInterval')
    window.localStorage.clear()
  })

  it('should start the timer', () => {
    const { result } = renderHook(() => useTimer())

    act(() => {
      result.current.startTimer()
    })

    expect(result.current.isTimerRunning).toBe(true)
  })

  it('counts the time after the timer start', () => {
    const { result } = renderHook(() => useTimer())

    act(() => {
      result.current.startTimer()
    })

    act(() => {
      const oneHour = 3600000
      const fiftyMinutes = 60000 * 50
      const thirtySeconds = 30000

      jest.advanceTimersByTime(oneHour + fiftyMinutes + thirtySeconds)
    })

    expect(result.current.hours).toBe(1)
    expect(result.current.minutes).toBe(50)
    expect(result.current.seconds).toBe(30)
  })

  it('stops the timer and reset the time', () => {
    const { result } = renderHook(() => useTimer())

    act(() => {
      result.current.startTimer()
    })

    act(() => {
      jest.advanceTimersByTime(300000)
    })

    act(() => {
      result.current.stopTimer()
    })

    expect(result.current.seconds).toBe(0)
    expect(result.current.isTimerRunning).toBe(false)
  })

  it('adds the startTime to the local storage', () => {
    const { result } = renderHook(() => useTimer())

    act(() => {
      result.current.startTimer()
    })

    expect(window.localStorage.getItem('timer_start')).toBe(result.current.startTime.toString())
  })

  it('clears the startTime on the local storage', () => {
    const { result } = renderHook(() => useTimer())

    act(() => {
      result.current.startTimer()
      result.current.stopTimer()
    })

    expect(window.localStorage.getItem('timer_start')).toBe(null)
  })
})
