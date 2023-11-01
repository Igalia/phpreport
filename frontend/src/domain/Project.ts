export type Project = {
  id: number
  area_id: number
  customer_id: number
  description: string
  is_active: boolean
  init: string | null
  end: string | null
  invoice: number | null
  estimated_hours: number | null
  moved_hours: number | null
  project_type: string | null
  schedule_type: string | null
}
