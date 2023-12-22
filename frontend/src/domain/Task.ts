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
    projectName: z.string(),
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

export const getOverlappingTasks = (
  { startTime, endTime, date }: { startTime: string; endTime: string; date: string },
  tasks: Array<Task>
) => {
  let message = ''

  const overlappingTasks = tasks.filter((task) => {
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
