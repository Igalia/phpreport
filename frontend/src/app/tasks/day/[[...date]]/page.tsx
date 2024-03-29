import { DayView } from './DayView'
import { makeGetProjects } from '@/infra/project/getProjects'
import { makeGetTaskTypes } from '@/infra/taskType/getTaskTypes'
import { makeGetTemplates } from '@/infra/template/getTemplates'
import { serverFetch } from '@/infra/lib/serverFetch'
import { authOptions } from '@/app/api/auth/[...nextauth]/authOptions'
import { getServerSession } from 'next-auth'
import { unstable_cache } from 'next/cache'

const getPageData = unstable_cache(async () => {
  const apiClient = await serverFetch()
  const session = await getServerSession(authOptions)
  const { id: userId } = session!.user

  const getProjects = makeGetProjects(apiClient)
  const getTaskTypes = makeGetTaskTypes(apiClient)
  const getTemplates = makeGetTemplates(apiClient)

  return await Promise.all([getProjects(), getTaskTypes(), getTemplates({ userId })])
}, ['day-view'])

export default async function Tasks() {
  const [projects, taskTypes, templates] = await getPageData()

  return <DayView projects={projects} taskTypes={taskTypes} templates={templates} />
}
