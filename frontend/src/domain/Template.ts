import { z } from 'zod'

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

export const TemplateIntent = z.object({
  name: z.string().min(1, { message: 'Name is required' }),
  story: z.string().nullable(),
  description: z.string().nullable(),
  taskType: z.string().nullable(),
  projectId: z.number().nullable(),
  userId: z.number(),
  isGlobal: z.boolean().nullable(),
  startTime: z.string().min(1, { message: 'Start time is required' }),
  endTime: z.string().min(1, { message: 'End time is required ' }),
  id: z.number().optional()
})

export type TemplateIntent = z.infer<typeof TemplateIntent>
