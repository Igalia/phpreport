import { TaskForm } from './TaskForm'
import { getProjects } from './getProjects'
import { getTaskTypes } from './getTaskTypes'

export default async function Tasks() {
  const projectsData = getProjects()
  const taskTypesData = getTaskTypes()

  const [projects, taskTypes] = await Promise.all([projectsData, taskTypesData])

  return <TaskForm projects={projects} taskTypes={taskTypes} />
}
