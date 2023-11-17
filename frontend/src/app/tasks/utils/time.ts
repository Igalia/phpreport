export const convertTimeToMinutes = (time: string) => {
  const [hour, minute] = time.split(':')

  return parseInt(hour) * 60 + parseInt(minute)
}

export const getTimeDifference = (startMinutes: number, endMinutes: number) => {
  const timeDiff = endMinutes - startMinutes

  const hours = Math.floor(timeDiff / 60)
  const minutes = timeDiff % 60

  return `${hours}h ${minutes}m`
}
