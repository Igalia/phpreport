import { Task, TaskIntent, validateTask } from '@/domain/Task'
import { ApiClient } from '../lib/apiClient'

import { BaseError } from '@/_lib/errors/BaseError'

const createTaskError = (message = 'Failed to create task') => {
  const name = 'CreateTaskError'

  return new BaseError({ message, name, code: name })
}

export const makeCreateTask =
  (apiClient: ApiClient) => async (task: TaskIntent, tasks: Array<Task>) => {
    const validation = validateTask(task, tasks)

    if (!validation.success) {
      throw createTaskError(validation.message)
    }

    try {
      const response = await apiClient('/v1/timelog/tasks', {
        method: 'POST',
        body: JSON.stringify(task)
      })

      if (!response.ok) {
        throw createTaskError()
      }

      return (await response.json()) as Task
    } catch (e) {
      throw createTaskError()
    }
  }
