import { getProjects } from '@/infra/project/getProjects'
import { serverFetch } from '@/infra/lib/serverFetch'
import { ProjectSelection } from './components/ProjectSelection'

const getPageData = async () => {
  const apiClient = await serverFetch()

  return getProjects(apiClient)
}

export default async function ProjectManagement() {
  const projects = await getPageData()

  return <ProjectSelection projects={projects} />
}
