import { useState } from 'react'
import Switch, { switchClasses } from '@mui/joy/Switch'
import Typography from '@mui/joy/Typography'
import { SxProps } from '@mui/joy/styles/types'
import { WeatherSunny20Filled, WeatherMoon20Filled } from '@fluentui/react-icons'
import Box from '@mui/joy/Box'

type DarkModeSwitchProps = {
  sx: SxProps
}

type SwitchComponentProps = {
  icon: typeof WeatherMoon20Filled
  children: React.ReactNode
  sx?: SxProps
}

const TrackText = ({ icon: Icon, children, sx }: SwitchComponentProps) => (
  <Box sx={{ display: { xs: 'none', sm: 'flex' }, alignItems: 'center', gap: '24px', ...sx }}>
    <Icon color="#00396d" />
    <Typography sx={{ color: '#b3cfe9' }}>{children}</Typography>
  </Box>
)

const SwitchThumb = ({ icon: Icon, children }: SwitchComponentProps) => (
  <Box
    sx={{
      display: 'flex',
      alignItems: 'center',
      gap: '30px',
      width: '100%',
      height: '100%',
      p: '0 10px',
      border: '2px solid #172E4D',
      borderRadius: '20px'
    }}
  >
    <Icon color="#b3cfe9" />
    <Typography sx={{ display: { xs: 'none', sm: 'block' }, color: '#F0F3F7' }}>
      {children}
    </Typography>
  </Box>
)

export const DarkModeSwitch = ({ sx }: DarkModeSwitchProps) => {
  const [checked, setChecked] = useState(false)

  return (
    <Switch
      onChange={() => setChecked((prevState) => !prevState)}
      slotProps={{
        input: { 'aria-label': 'Dark mode' },
        track: {
          children: (
            <>
              <TrackText sx={{ ml: '10px' }} icon={WeatherSunny20Filled}>
                Light
              </TrackText>
              <TrackText sx={{ mr: '10px' }} icon={WeatherMoon20Filled}>
                Dark
              </TrackText>
            </>
          )
        },
        thumb: {
          children: !checked ? (
            <SwitchThumb icon={WeatherSunny20Filled}>Light</SwitchThumb>
          ) : (
            <SwitchThumb icon={WeatherMoon20Filled}>Dark</SwitchThumb>
          )
        }
      }}
      sx={{
        '--Switch-thumbBackground': '#00396d',
        '--Switch-trackBackground': '#030D1A',
        '&:hover': {
          '--Switch-trackBackground': '#030D1A'
        },
        [`&.${switchClasses.checked}`]: {
          '--Switch-thumbBackground': '#00396d',
          '--Switch-trackBackground': '#030D1A',
          '&:hover': {
            '--Switch-trackBackground': '#030D1A'
          }
        },
        '--Switch-thumbWidth': { xs: '40px', sm: '148px' },
        '--Switch-thumbSize': { xs: '20px', sm: '30px' },
        '--Switch-trackWidth': { xs: '60px', sm: '260px' },
        '--Switch-thumbRadius': { xs: '', sm: '20px' },
        '--Switch-trackHeight': { xs: '', sm: '40px' },
        '--Switch-trackRadius': { xs: '', sm: '25px' },
        ...sx
      }}
    />
  )
}
