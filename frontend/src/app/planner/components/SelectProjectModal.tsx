import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { Autocomplete, Sheet, Typography, Modal } from '@mui/joy'

import { Button } from '@mui/joy'
import { Project } from '@/domain/Project'

type SelectProjectProps = {
  open: boolean
  projects: Array<Project>
}

export const SelectProjectModal = ({ open, projects }: SelectProjectProps) => {
  const router = useRouter()
  const [selectedProject, setSelectedProject] = useState<Project | null>(null)

  return (
    <Modal
      sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center' }}
      open={open}
      aria-labelledby="confirmation-modal-title"
      aria-describedby="modal-content"
    >
      <Sheet
        variant="outlined"
        component="form"
        action={() => {
          router.push(`/planner/${selectedProject?.id}`)
        }}
        sx={{
          minWidth: 300,
          minHeight: 200,
          borderRadius: 'md',
          padding: 3,
          boxShadow: 'lg',
          display: 'flex',
          flexDirection: 'column',
          gap: '8px'
        }}
      >
        <Typography
          component="h2"
          id="confirmation-modal-title"
          level="h4"
          textColor="inherit"
          fontWeight="lg"
        >
          Select Project To Manage
        </Typography>
        <Autocomplete
          name="project"
          value={selectedProject}
          onChange={(_, newValue) => {
            setSelectedProject(newValue)
          }}
          options={projects}
          getOptionLabel={(project) => project.description}
          getOptionKey={(project) => project.id}
          autoSelect
          autoComplete
        />
        <Button type="submit" disabled={!selectedProject} sx={{ margin: 'auto 0 0' }}>
          Select Project
        </Button>
      </Sheet>
    </Modal>
  )
}
