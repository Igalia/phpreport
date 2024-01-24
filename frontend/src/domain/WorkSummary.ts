
export type WorkSummary = {
  today?: number | null
  week?: number | null
  todayText: string
  weekText: string
  projectSummaries?: Array<ProjectTaskSummary> | null
  vacationAvailable: number
  vacationAvailableText: string
  vacationScheduled: number
  vacationScheduledText: string
  vacationPending: number
  vacationPendingText: string
  vacationUsed: number
  vacationUsedText: string
  expectedHoursYear: number
  expectedHoursToDate: number
  expectedHoursWeek: number
  workedHoursYear: number
}

export type ProjectTaskSummary = {
  projectId: number
  project: string
  todayTotal: string
  todayText: string
  weekTotal: string
  weekText: string
  isVacation: boolean
}
