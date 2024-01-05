import { z } from 'zod'

function getMinutes(time: string) {
  const hours = Number(time.split(':')[0])
  const minutes = Number(time.split(':')[1])
  return hours * 60 + minutes
}

export type Task = {
  id: number
  userId: number
  projectId: number
  taskType: string | null
  story: string | null
  description: string | null
  startTime: string
  endTime: string
  date: string
  customerName: string
  projectName: string
}

export const TaskIntent = z
  .object({
    id: z.number().optional(),
    userId: z.number(),
    projectId: z.number().nullable(),
    taskType: z.string().nullable(),
    story: z.string().nullable(),
    description: z.string().nullable(),
    startTime: z.string().min(1, { message: 'Start time is required' }),
    endTime: z.string().min(1, { message: 'End time is required ' }),
    date: z.string()
  })
  .superRefine((obj, ctx) => {
    if (getMinutes(obj.startTime) > getMinutes(obj.endTime)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        message: 'End time must be after Start time'
      })
    }

    if (obj.projectId === null) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        message: 'Project is required'
      })
    }
  })
export type TaskIntent = z.infer<typeof TaskIntent>

type AnyTask = Task | TaskIntent

export const getOverlappingTasks = (
  { startTime, endTime, date, id }: AnyTask,
  tasks: Array<Task>
) => {
  let message = ''

  const overlappingTasks = tasks.filter((task) => {
    if (id === task.id) {
      return false
    }

    const haveEqualDate = task.date === date
    const overlapsTasksDuration =
      getMinutes(endTime) > getMinutes(task.startTime) &&
      getMinutes(startTime) < getMinutes(task.endTime)
    const coincidesInitOrEndTimes =
      getMinutes(startTime) == getMinutes(task.startTime) ||
      getMinutes(endTime) == getMinutes(task.endTime)

    const isOverlapping = haveEqualDate && (overlapsTasksDuration || coincidesInitOrEndTimes)

    if (isOverlapping) {
      message += `Task from ${startTime} to ${endTime} overlaps with task from ${task.startTime} to ${task.endTime}. `
    }

    return isOverlapping
  })

  return { overlappingTasks, message }
}

export const validateTask = (task: AnyTask, tasks: Array<Task>) => {
  const validation = TaskIntent.safeParse(task)

  if (!validation.success) {
    const error = validation.error.issues.reduce((acc, issue) => `${acc} ${issue.message}`, '')

    return { success: false, message: error }
  }

  const { overlappingTasks, message } = getOverlappingTasks(task, tasks)

  if (overlappingTasks.length > 0) {
    return { success: false, message }
  }

  return { success: true, message: 'Valid task' }
}
