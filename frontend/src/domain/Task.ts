import { z } from 'zod'

export type Task = {
  id: number
  userId: number
  projectId: number
  taskType?: string | null
  story: string
  description: string
  startTime: string
  endTime: string
  date: string
  init: number
  end: number
  projectName: string
  customerName: string
}

export const TaskIntent = z.object({
  userId: z.number(),
  projectId: z.string().min(1, { message: 'Project is required' }),
  taskType: z.string(),
  story: z.string(),
  description: z.string(),
  startTime: z.string().min(1, { message: 'startTime is required' }),
  endTime: z.string().min(1, { message: 'endTime is required ' }),
  date: z.string()
})

export type TaskIntent = z.infer<typeof TaskIntent>

function getMinutes(time: string) {
  const hours = Number(time.split(':')[0])
  const minutes = Number(time.split(':')[1])
  return hours * 60 + minutes
}

export const getOverlappingTasks = (newTask: TaskIntent, tasks: Array<Task>) => {
  let message = ''

  const overlappingTasks = tasks.filter((task) => {
    const haveEqualDate = task.date === newTask.date
    const overlapsTasksDuration =
      getMinutes(newTask.endTime) > getMinutes(task.startTime) &&
      getMinutes(newTask.startTime) < getMinutes(task.endTime)
    const coincidesInitOrEndTimes =
      getMinutes(newTask.startTime) == getMinutes(task.startTime) ||
      getMinutes(newTask.endTime) == getMinutes(task.endTime)

    const isOverlapping = haveEqualDate && (overlapsTasksDuration || coincidesInitOrEndTimes)

    if (isOverlapping) {
      message += `Task from ${newTask.startTime} to ${newTask.endTime} overlaps with task from ${task.startTime} to ${task.endTime}. `
    }

    return isOverlapping
  })

  return { overlappingTasks, message }
}
