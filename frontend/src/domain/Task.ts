import { z } from 'zod'

function getMinutes(time: string) {
  const hours = Number(time.split(':')[0])
  const minutes = Number(time.split(':')[1])
  return hours * 60 + minutes
}

const timeValidation = (startTime: string, endTime: string, ctx: z.RefinementCtx) => {
  if (getMinutes(startTime) > getMinutes(endTime)) {
    ctx.addIssue({
      code: z.ZodIssueCode.custom,
      message: 'End time must be after Start time'
    })
  }
}

export const Task = z
  .object({
    id: z.number(),
    userId: z.number(),
    projectId: z.number().min(1, { message: 'Project is required' }),
    taskType: z.string().nullable(),
    story: z.string(),
    description: z.string(),
    startTime: z.string().min(1, { message: 'Start time is required' }),
    endTime: z.string().min(1, { message: 'End time is required ' }),
    date: z.string(),
    customerName: z.string(),
    projectName: z.string()
  })
  .superRefine((obj, ctx) => {
    timeValidation(obj.startTime, obj.endTime, ctx)
  })

export type Task = z.infer<typeof Task>

export const TaskIntent = z
  .object({
    userId: z.number(),
    projectId: z.string().min(1, { message: 'Project is required' }),
    taskType: z.string().optional(),
    story: z.string(),
    description: z.string(),
    startTime: z.string().min(1, { message: 'Start time is required' }),
    endTime: z.string().min(1, { message: 'End time is required ' }),
    date: z.string()
  })
  .superRefine((obj, ctx) => {
    timeValidation(obj.startTime, obj.endTime, ctx)
  })
export type TaskIntent = z.infer<typeof TaskIntent>

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
