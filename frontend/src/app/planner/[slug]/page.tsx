import { Breadcrumbs, Chip, Typography } from '@mui/joy'
import Link from 'next/link'

import { getProjectAllocation } from '@/infra/projectAllocation/getProjectAllocation'
import { getProjects } from '@/infra/project/getProjects'

import { serverFetch } from '@/infra/lib/serverFetch'

const getPageData = async (id: string) => {
  const apiClient = await serverFetch()
  return await Promise.all([getProjectAllocation(apiClient, id), getProjects(apiClient)])
}

export default async function Page({ params }: { params: { slug: string } }) {
  const [projectAllocations, projects] = await getPageData(params.slug)

  const project = projects.find((p) => p.id === Number(params.slug))
  const projectStats = { plannedHours: projectAllocations?.plannedHours }

  return (
    <>
      <Breadcrumbs aria-label="breadcrumbs">
        <Link color="neutral" href="#">
          Projects
        </Link>
        <Link color="neutral" href="#">
          <Typography
            level="body-lg"
            endDecorator={
              <Chip component="span" size="sm" sx={{ textDecoration: 'underline' }}>
                <Link href="/planner">Switch</Link>
              </Chip>
            }
            justifyContent="center"
          >
            {project?.description}
          </Typography>
        </Link>
        <Typography>Resource Planner</Typography>
      </Breadcrumbs>
    </>
  )
}
