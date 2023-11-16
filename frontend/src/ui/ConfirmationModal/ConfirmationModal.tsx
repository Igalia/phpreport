import Modal from '@mui/joy/Modal'
import ModalClose from '@mui/joy/ModalClose'
import Sheet from '@mui/joy/Sheet'
import Box from '@mui/joy/Box'
import Button from '@mui/joy/Button'
import Typography from '@mui/joy/Typography'

type ConfirmationModalProps = {
  open: boolean
  closeModal: () => void
  confirmAction: () => void
  title: string
  content: string
  confirmText: string
}

export const ConfirmationModal = ({
  open,
  closeModal,
  confirmAction,
  title,
  content,
  confirmText
}: ConfirmationModalProps) => {
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
          {title}
        </Typography>
        <Typography id="modal-content">{content}</Typography>
        <Box
          sx={{
            display: 'flex',
            alignSelf: 'end',
            width: '100%',
            justifyContent: 'flex-end',
            gap: '16px',
            margin: 'auto 0 0'
          }}
        >
          <Button onClick={() => closeModal()} variant="outlined">
            Cancel
          </Button>
          <Button onClick={() => confirmAction()}>{confirmText}</Button>
        </Box>
      </Sheet>
    </Modal>
  )
}
