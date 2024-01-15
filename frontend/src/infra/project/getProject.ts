import { ApiClient } from '@/infra/lib/apiClient'
import { Project } from '@/domain/Project'

export const makeGetProject =
  (apiClient: ApiClient) =>
  async (projectId: string): Promise<Project> => {
    const response = await apiClient(`/v1/projects/${projectId}`)

    if (!response.ok) {
      throw new Error('Failed to fetch Project')
    }

    return response.json()
  }
