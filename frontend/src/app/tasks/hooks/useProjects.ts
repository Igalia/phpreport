import { useQuery } from '@tanstack/react-query'
import { useAuth } from 'react-oidc-context'

import { apiClient } from '@/infra/apiClient'

type Project = {
  id: string
  area_id: number
  customer_id: number
  description: string
  is_active: boolean
  init?: string
  end?: string
  invoice?: number
  estimated_hours?: number
  moved_hours?: number
  project_type?: string
  schedule_type?: string
}

const fetchProjects = (token: string): Promise<Array<Project>> => {
  return apiClient(token)
    .get('/v1/projects')
    .then((response) => response.data)
}

export const useProjects = () => {
  const auth = useAuth()
  const token = auth.user?.access_token || ''

  const { data } = useQuery({
    queryKey: ['projects', token],
    queryFn: () => {
      return fetchProjects(token)
    },
    initialData: []
  })

  return data
}
