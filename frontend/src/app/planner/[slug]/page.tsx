import { Breadcrumbs, Chip, Link, Typography } from '@mui/joy'

import { makeGetProject } from '@/infra/project/getProject'
import { serverFetch } from '@/infra/lib/serverFetch'
import { Planner } from './components/Planner'
import { makeGetProjectStats } from '@/infra/project/getProjectStats'

const getPageData = async (id: string) => {
  const apiClient = await serverFetch()
  const getProject = makeGetProject(apiClient)
  const getProjectStats = makeGetProjectStats(apiClient)
  return await Promise.all([getProject(id), getProjectStats(id)])
}

export default async function Page({ params }: { params: { slug: string } }) {
  const [project, projectStats] = await getPageData(params.slug)

  return (
    <>
      <Breadcrumbs aria-label="breadcrumbs">
        <Link color="neutral" href="#">
          Projects
        </Link>
        <Typography>
          {project?.description}{' '}
          <Link href="/planner" sx={{ fontSize: '0.8rem' }} underline="always">
            Switch
          </Link>
        </Typography>
        <Typography>Resource Planner</Typography>
      </Breadcrumbs>
      {project ? (
        <Planner projectMetada={project} projectStats={projectStats} />
      ) : (
        <div>
          <p>Project not found.</p>
          <Link href="/planner" underline="always">
            Please select a Project.
          </Link>
        </div>
      )}
    </>
  )
}
