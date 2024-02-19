import { useState } from 'react'
import { Sheet, Typography, Modal, ModalClose } from '@mui/joy'
import { Input } from '@/ui/Input/Input'
import { Task } from '@/domain/Task'
import { createTemplate } from '../actions/createTemplate'
import { useAlert } from '@/ui/Alert/useAlert'
import { SubmitButton } from '@/ui/SubmitButton/SubmitButton'
import { SaveCopy24Filled } from '@fluentui/react-icons'
import { IconButton } from './IconButton'

type SaveAsTemplateProps = {
  task: Task
}

export const SaveTemplateModal = ({ task }: SaveAsTemplateProps) => {
  const { showError, showSuccess } = useAlert()
  const [templateModalOpen, setTemplateModalOpen] = useState(false)

  const closeModal = () => setTemplateModalOpen(false)

  return (
    <>
      <IconButton
        onClick={() => setTemplateModalOpen(true)}
        icon={SaveCopy24Filled}
        description="save task as template"
      />
      <Modal
        sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center' }}
        open={templateModalOpen}
        onClose={closeModal}
        aria-labelledby="confirmation-modal-title"
      >
        <Sheet
          variant="outlined"
          component="form"
          action={async (formData: FormData) => {
            console.log('test')
            const template = await createTemplate(task, formData)
            if (template.error) {
              showError(template.error)
            } else {
              showSuccess('Template created')
            }
            closeModal()
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
          <ModalClose></ModalClose>
          <Typography
            component="h2"
            id="confirmation-modal-title"
            level="h4"
            textColor="inherit"
            fontWeight="lg"
          >
            Save task as template
          </Typography>
          <Input name="name" label="Template name" required />
          <SubmitButton sx={{ margin: 'auto 0 0' }}>Save template</SubmitButton>
        </Sheet>
      </Modal>
    </>
  )
}
