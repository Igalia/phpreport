import { useQuery } from '@tanstack/react-query'
import { getTasks } from '@/infra/task/getTasks'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { format } from 'date-fns'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'

export const useGetTasks = () => {
  const apiClient = useClientFetch()
  const { id: userId } = useGetCurrentUser()
  const today = format(new Date(), 'yyyy-MM-dd')

  const { data } = useQuery({
    queryKey: ['tasks', userId],
    queryFn: () => getTasks(apiClient, { userId, start: today, end: today }),
    initialData: []
  })

  return { tasks: data }
}
