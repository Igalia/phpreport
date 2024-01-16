'use client'

import { Project, ProjectStats } from '@/domain/Project'
import { ProjectHeader } from './ProjectHeader'

type PlannerProps = {
  projectMetada: Project
  projectStats: ProjectStats
}

export const Planner = ({ projectMetada, projectStats }: PlannerProps) => {
  return <ProjectHeader project={projectMetada} projectStats={projectStats} />
}
