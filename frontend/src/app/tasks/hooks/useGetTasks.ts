import { useQuery } from '@tanstack/react-query'
import { makeGetTasks } from '@/infra/task/getTasks'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { format } from 'date-fns'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'
import { useDateParam } from './useDateParam'

export const useGetTasks = () => {
  const apiClient = useClientFetch()
  const { date } = useDateParam()
  const { id: userId } = useGetCurrentUser()
  const selectedDate = format(date, 'yyyy-MM-dd')
  const getTasks = makeGetTasks(apiClient)

  const { data = [], isLoading } = useQuery({
    queryKey: ['tasks', userId, selectedDate],
    queryFn: () => getTasks({ userId, startTime: selectedDate, endTime: selectedDate })
  })

  return { tasks: data, isLoading }
}
