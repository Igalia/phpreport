export type Template = {
  id: number
  name: string
  story: string | null
  description: string | null
  taskType: string | null
  userId: number | null
  projectId: number | null
  isGlobal: boolean
  startTime: string | null
  endTime: string | null
}
