import Box from '@mui/joy/Box'
import Chip from '@mui/joy/Chip'

import Grid from '@mui/joy/Grid'
import Typography from '@mui/joy/Typography'
import { Project, ProjectStats } from '@/domain/Project'

type ProjectHeaderProps = {
  project: Project
  projectStats: ProjectStats
}

export const ProjectHeader = ({ project, projectStats }: ProjectHeaderProps) => {
  return (
    <Box
      sx={{
        textAlign: 'center',
        padding: '2rem',
        borderBottom: '1px solid #ebebeb',
        borderTop: '1px solid #ebebeb',
        backgroundColor: '#f7f7f7'
      }}
    >
      <Typography
        level="h1"
        sx={{
          textAlign: 'left',
          marginBottom: '1rem'
        }}
      >
        {project.description}
        <Chip sx={{ ml: 1 }} color={project.isActive ? 'success' : 'danger'}>
          {project.isActive ? 'Active' : 'Inactive'}
        </Chip>
      </Typography>
      <Grid
        container
        spacing={2}
        sx={{
          flexGrow: 1
        }}
      >
        <Grid
          xs={4}
          sx={{
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'center',
            textAlign: 'center',
            alignItems: 'center'
          }}
        >
          <Typography
            fontSize="xl4"
            lineHeight={1}
            endDecorator={
              <Typography fontSize="lg" textColor="text.secondary">
                h
              </Typography>
            }
          >
            {Number(projectStats.loggedHours)}/{Number(projectStats.plannedHours)}
          </Typography>
          <Typography>Actuals vs Planned</Typography>
        </Grid>
        <Grid
          xs={4}
          sx={{
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'center',
            alignItems: 'center'
          }}
        >
          <Typography
            fontSize="xl4"
            lineHeight={1}
            endDecorator={
              <Typography fontSize="lg" textColor="text.secondary">
                h
              </Typography>
            }
            sx={{ alignItems: 'flex-start' }}
          >
            {Number(projectStats.plannedHours)}/{Number(project.estimatedHours)}
          </Typography>
          <Typography>Planned vs Sold</Typography>
        </Grid>
        <Grid
          xs={4}
          sx={{
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'center',
            alignItems: 'center'
          }}
        >
          <Typography fontSize="xl4" lineHeight={1} sx={{ alignItems: 'flex-start' }}>
            {Number(projectStats.avgFTE)}
          </Typography>
          <Typography>
            Average <abbr title="full-time equivalent">FTE</abbr>s
          </Typography>
        </Grid>
      </Grid>
    </Box>
  )
}
