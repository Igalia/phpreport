import Box from '@mui/joy/Box'
import JoyAlert from '@mui/joy/Alert'
import Button from '@mui/joy/Button'

import { useAlert } from './useAlert'

export const Alert = () => {
  const {
    submitAlert: { showAlert, type, message },
    closeAlert
  } = useAlert()

  return (
    <Box
      sx={{
        position: 'fixed',
        bottom: { xs: '20px', sm: '50px' },
        right: { xs: '20px', sm: '100px' },
        zIndex: 9,
        width: '300px',
        visibility: showAlert ? 'visible' : 'hidden',
        opacity: showAlert ? '100' : '0',
        transition: 'opacity 0.6s'
      }}
      aria-hidden={!showAlert}
    >
      <JoyAlert
        color={type}
        endDecorator={
          <Button onClick={closeAlert} size="sm" variant="solid" color={type}>
            Close
          </Button>
        }
      >
        {message}
      </JoyAlert>
    </Box>
  )
}
