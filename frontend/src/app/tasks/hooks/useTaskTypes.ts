import { useQuery } from '@tanstack/react-query'
import { useAuth } from 'react-oidc-context'
import { apiClient } from '@/infra/apiClient'

type TaskType = {
  slug: string
  name: string
  active: boolean
}

const fetchTaskTypes = (token: string): Promise<Array<TaskType>> => {
  return apiClient(token)
    .get('/v1/timelog/task_types/')
    .then((response) => response.data)
}

export const useTaskTypes = () => {
  const auth = useAuth()
  const token = auth.user?.access_token || ''

  const { data } = useQuery({
    queryKey: ['taskTypes', token],
    queryFn: () => {
      return fetchTaskTypes(token)
    },
    initialData: []
  })

  return data
}
