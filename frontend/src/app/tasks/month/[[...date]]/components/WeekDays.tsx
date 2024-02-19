import { Typography } from '@mui/joy'

const WEEK_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']

export const WeekDays = () => {
  return WEEK_DAYS.map((day) => {
    return (
      <Typography
        sx={{
          textAlign: 'center',
          padding: { xs: '8px', sm: '22px 16px' },
          borderBottom: '1px solid #C4C6D0',
          borderTop: '1px solid #C4C6D0',
          borderRight: '1px solid #C4C6D0',
          ':first-of-type': { borderLeft: '1px solid #C4C6D0' },
          ':nth-of-type(7n)': { borderRight: { sm: 'none' }, borderLeft: { sm: 'none' } },
          fontWeight: '600',
          backgroundColor: '#fff',
          fontSize: { xs: '0px', sm: '1rem' },
          ':first-letter': { fontSize: '1rem' }
        }}
        component="div"
        key={day}
      >
        {day}
      </Typography>
    )
  })
}
