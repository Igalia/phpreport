import { Button } from '@mui/joy'
import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'
import { SaveCopy24Filled } from '@fluentui/react-icons'

type IconButtonProps = {
  description: string
  onClick: () => void
  icon: typeof SaveCopy24Filled
}

export const IconButton = ({ icon: Icon, description, onClick }: IconButtonProps) => {
  return (
    <Button
      sx={{
        background: 'transparent',
        padding: '0',
        height: '24px',
        width: '24px',
        ':hover': { background: 'transparent' }
      }}
      onClick={onClick}
    >
      <Icon color="#2f3338" />
      <ScreenReaderOnly>{description}</ScreenReaderOnly>
    </Button>
  )
}
