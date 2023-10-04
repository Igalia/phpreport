import Button from '@mui/joy/Button'
import Box from '@mui/joy/Box'
import { ChevronRight16Filled } from '@fluentui/react-icons'
import { ScreenReaderOnly } from '@/ui/ScreenReaderOnly/ScreenReaderOnly'
import { SxProps } from '@mui/joy/styles/types'

type CollapseButtonProps = {
  onClick: () => void
  sx: SxProps
  iconSx: SxProps
}

export const CollapseButton = ({ onClick, sx, iconSx }: CollapseButtonProps) => {
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
      <Box sx={iconSx}>
        <ChevronRight16Filled primaryFill="black" />
      </Box>
      <ScreenReaderOnly>Expand/collapse menu</ScreenReaderOnly>
    </Button>
  )
}
