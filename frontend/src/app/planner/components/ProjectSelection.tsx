'use client'

import { Project } from '@/domain/Project'
import { SelectProjectModal } from './SelectProjectModal'

type ProjectSelectionProps = {
  projects: Array<Project>
}

export const ProjectSelection = ({ projects }: ProjectSelectionProps) => {
  return <SelectProjectModal open={true} projects={projects} />
}
