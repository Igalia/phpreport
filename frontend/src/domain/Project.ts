export type Project = {
  id: number
  areaId: number
  customerId: number
  description: string
  isActive: boolean
  init: string | null
  end: string | null
  invoice: number | null
  estimatedHours: number | null
  movedHours: number | null
  projectType: string | null
  scheduleType: string | null
}
