import { ApiClient } from '@/infra/lib/apiClient'
import { Project } from '@/domain/Project'

export const getProject = async (
  apiClient: ApiClient,
  projectId: string
): Promise<Array<Project>> => {
  const response = await apiClient(`/v1/projects/${projectId}`)

  if (!response.ok) {
    throw new Error('Failed to fetch Project')
  }

  return response.json()
}
