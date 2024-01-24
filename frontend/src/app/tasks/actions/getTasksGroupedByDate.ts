import { makeGetTasks } from '@/infra/task/getTasks'
import { serverFetch } from '@/infra/lib/serverFetch'
import { authOptions } from '@/app/api/auth/[...nextauth]/route'
import { getServerSession } from 'next-auth'

import { Task } from '@/domain/Task'

import { convertTimeToMinutes } from '../utils/time'

export const getTasksGroupedByDate = async (startTime: string, endTime: string, limit?: number) => {
  const apiClient = await serverFetch()
  const getTasks = makeGetTasks(apiClient)
  const session = await getServerSession(authOptions)
  const { id: userId } = session!.user

  const tasks = await getTasks({ userId, startTime, endTime, limit })

  // Group tasks by date and add the total time for that date
  return tasks.reduce<Record<Task['date'], { tasks: Array<Task>; time: number }>>((acc, task) => {
    const startTime = convertTimeToMinutes(task.startTime)
    const endTime = convertTimeToMinutes(task.endTime)
    const timeDiff = endTime - startTime

    if (acc[task.date]) {
      const { tasks, time } = acc[task.date]

      return { ...acc, [task.date]: { tasks: [...tasks, task], time: time + timeDiff } }
    }

    return { ...acc, [task.date]: { tasks: [task], time: timeDiff } }
  }, {})
}
