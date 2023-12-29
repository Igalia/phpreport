import { Sheet, Typography, Modal, ModalClose } from '@mui/joy'

import { Input } from '@/ui/Input/Input'
import { Task } from '@/domain/Task'
import { createTemplate } from '../actions/createTemplate'
import { useAlert } from '@/ui/Alert/useAlert'
import { SubmitButton } from '@/ui/SubmitButton/SubmitButton'

type SaveAsTemplateProps = {
  closeModal: () => void
  open: boolean
  task: Task
}

export const SaveTemplateModal = ({ closeModal, open, task }: SaveAsTemplateProps) => {
  const { showError, showSuccess } = useAlert()

  return (
    <Modal
      sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center' }}
      open={open}
      onClose={() => closeModal()}
      aria-labelledby="confirmation-modal-title"
      aria-describedby="modal-content"
    >
      <Sheet
        variant="outlined"
        component="form"
        action={async (formData: FormData) => {
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
  )
}
