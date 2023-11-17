import Box from '@mui/joy/Box'
import { TaskList } from './TaskList'
import { TaskForm } from './TaskForm'
import { getProjects } from '@/infra/project/getProjects'
import { getTaskTypes } from '@/infra/taskType/getTaskTypes'
import { serverFetch } from '@/infra/lib/serverFetch'

const getPageData = async () => {
  const apiClient = await serverFetch()

  return await Promise.all([getProjects(apiClient), getTaskTypes(apiClient)])
}

export default async function Tasks() {
  const [projects, taskTypes] = await getPageData()

  return (
    <Box
      sx={{
        display: 'flex',
        flexDirection: { xs: 'column', sm: 'row' },
        margin: '0 auto',
        gap: '30px',
        justifyContent: 'center',
        padding: { xs: '0 8px', sm: 0 }
      }}
    >
      <TaskForm projects={projects} taskTypes={taskTypes} />
      <TaskList />
    </Box>
  )
}
