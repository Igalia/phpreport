export type Task = {
  id: number
  userId: number
  projectId: string
  taskType: string
  story: string
  description: string
  startTime: string
  endTime: string
  date: string
}

export type TaskIntent = Omit<Task, 'id'>
