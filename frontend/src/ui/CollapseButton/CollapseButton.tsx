import Button from '@mui/joy/Button'
import { ChevronRight16Filled } from '@fluentui/react-icons'
import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'
import { SxProps } from '@mui/joy/styles/types'

type CollapseButtonProps = {
  onClick: () => void
  sx: SxProps
}

export const CollapseButton = ({ onClick, sx }: CollapseButtonProps) => {
  return (
    <Button
      size="sm"
      onClick={onClick}
      sx={{
        borderRadius: '50%',
        bgcolor: 'white',
        width: 32,
        height: 32,
        alignItems: 'center',
        justifyContent: 'center',
        display: 'flex',
        position: 'absolute',
        padding: 0,
        zIndex: 1,
        boxShadow: '0px 6px 6px #00000029',
        ...sx
      }}
    >
      <ChevronRight16Filled primaryFill="black" />
      <ScreenReaderOnly>Expand/collapse menu</ScreenReaderOnly>
    </Button>
  )
}
