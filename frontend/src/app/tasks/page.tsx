import { TaskForm } from './TaskForm'
import { getProjects } from '@/infra/project/getProjects'
import { getTaskTypes } from '@/infra/taskType/getTaskTypes'
import { getCurrentUser } from '@/infra/user/getCurrentUser'
import { serverFetch } from '@/infra/lib/apiClient'

const getPageData = async () => {
  const apiClient = await serverFetch()

  return await Promise.all([
    getProjects(apiClient),
    getTaskTypes(apiClient),
    getCurrentUser(apiClient)
  ])
}

export default async function Tasks() {
  const [projects, taskTypes, currentUser] = await getPageData()

  return <TaskForm projects={projects} taskTypes={taskTypes} userId={currentUser.id} />
}
