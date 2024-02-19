import { useParams } from 'next/navigation'
import { getDateFromParam } from '../utils/time'
import { useMemo } from 'react'

export const useDateParam = () => {
  const params = useParams()

  return useMemo(() => {
    const dateParam = params.date ? params.date[0] : ''
    const date = getDateFromParam(dateParam)
    return { date, dateParam }
  }, [params.date])
}
