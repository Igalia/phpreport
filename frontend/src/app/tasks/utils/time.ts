import { parseISO } from 'date-fns'

export const convertTimeToMinutes = (time: string) => {
  const [hour, minute] = time.split(':')

  return parseInt(hour) * 60 + parseInt(minute)
}

export const convertMinutesToTime = (timeInMinutes: number) => {
  const hours = Math.floor(timeInMinutes / 60)
  const minutes = timeInMinutes % 60

  return `${hours}h ${minutes}m`
}

export const getTimeDifference = (startMinutes: number, endMinutes: number) => {
  const timeDiff = endMinutes - startMinutes

  return convertMinutesToTime(timeDiff)
}

export const getDateFromParam = (dateParam?: string | null) => {
  return dateParam ? parseISO(dateParam) : new Date()
}
