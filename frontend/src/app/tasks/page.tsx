import { TaskForm } from './TaskForm'
import { getProjects } from '@/infra/project/getProjects'
import { getTaskTypes } from '@/infra/taskType/getTaskTypes'
import { serverFetch } from '@/infra/lib/serverFetch'
import { authOptions } from '@/app/api/auth/[...nextauth]/route'
import { getServerSession } from 'next-auth'
import { getTemplates } from '@/infra/template/getTemplates'
import { TaskList } from './TaskList'

const getPageData = async () => {
  const apiClient = await serverFetch()
  const session = await getServerSession(authOptions)
  const { id: userId } = session!.user

  return await Promise.all([
    getProjects(apiClient),
    getTaskTypes(apiClient),
    getTemplates(apiClient, { userId })
  ])
}

export default async function Tasks() {
  const [projects, taskTypes, templates] = await getPageData()

  return (
    <TaskForm
      projects={projects}
      taskTypes={taskTypes}
      templates={templates}
      taskList={<TaskList />}
    />
  )
}
