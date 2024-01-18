import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { Autocomplete, FormControl, FormLabel, Sheet, Modal } from '@mui/joy'

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
    >
      <Sheet
        sx={{
          minWidth: 300,
          minHeight: 200,
          borderRadius: 'md',
          padding: 3,
          boxShadow: 'lg',
          display: 'flex',
          flexDirection: 'column',
          justifyContent: 'center',
          gap: '8px'
        }}
      >
        <FormControl>
          <FormLabel id="confirmation-modal-title" sx={{ fontSize: '1.2rem' }}>
            Select Project To Manage
          </FormLabel>
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
        </FormControl>
        <Button
          disabled={!selectedProject}
          onClick={() => router.push(`/planner/${selectedProject?.id}`)}
        >
          Select Project
        </Button>
      </Sheet>
    </Modal>
  )
}
