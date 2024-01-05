import { Task, TaskIntent, validateTask } from '@/domain/Task'
import { ApiClient } from '../lib/apiClient'

import { BaseError } from '@/_lib/errors/BaseError'

const editTaskError = (message = 'Failed to edit task') => {
  const name = 'EditTaskError'

  return new BaseError({ message, name, code: name })
}

export const makeEditTask =
  (apiClient: ApiClient) => async (task: TaskIntent, tasks: Array<Task>) => {
    const validation = validateTask(task, tasks)

    if (!validation.success) {
      throw editTaskError(validation.message)
    }

    try {
      const response = await apiClient(`/v1/timelog/tasks/${task.id}`, {
        method: 'PUT',
        body: JSON.stringify(task)
      })

      if (!response.ok) {
        throw editTaskError()
      }

      return (await response.json()) as Task
    } catch (e) {
      throw editTaskError()
    }
  }
