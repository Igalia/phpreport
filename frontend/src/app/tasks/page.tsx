import { TaskForm } from './TaskForm'
import { getProjects } from '@/infra/project/getProjects'
import { getTaskTypes } from '@/infra/taskType/getTaskTypes'

export default async function Tasks() {
  const projectsData = getProjects()
  const taskTypesData = getTaskTypes()

  const [projects, taskTypes] = await Promise.all([projectsData, taskTypesData])

  return <TaskForm projects={projects} taskTypes={taskTypes} />
}
