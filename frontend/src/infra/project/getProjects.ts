import { apiClient } from '@/infra/lib/apiClient'
import { Project } from '@/domain/Project'

export const getProjects = async (): Promise<Array<Project>> => {
  const response = await apiClient('/v1/projects/')

  if (!response.ok) {
    throw new Error('Failed to fetch Projects')
  }

  return response.json()
}
