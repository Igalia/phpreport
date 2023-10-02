import { useQuery } from '@tanstack/react-query'
import { useAuth } from 'react-oidc-context'

type TaskType = {
  slug: string
  name: string
  active: boolean
}

// Temporary disable so we don't need to change the fetch api for now.
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const fetchTaskTypes = (token: string): Promise<Array<TaskType>> => {
  // return apiClient(token)
  //   .get('/v1/timelog/task_types/')
  //   .then((response) => response.data)
  const mockData = Promise.resolve([
    { name: 'mock task type', slug: 'mock-test', active: true },
    { name: 'mock task type 2', slug: 'mock-test-2', active: true }
  ])
  return mockData
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
