import { ApiClient } from '../lib/apiClient'

export const deleteTask = async (taskId: number, apiClient: ApiClient): Promise<string> => {
  return apiClient(`/v1/timelog/tasks/${taskId}`, {
    method: 'DELETE'
  })
    .then((response) => {
      if (!response.ok) {
        throw Error(response.statusText)
      }

      return response.statusText
    })
    .catch((e) => {
      throw new Error(e)
    })
}
