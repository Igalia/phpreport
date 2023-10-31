import { IMask } from 'react-imask'
import { MaskedSelect, MaskedSelectProps } from '@/ui/Select/MaskedSelect'

const timeOptions = () => {
  const hours = Array.from({ length: 24 }, (_, i) => i)
  const minutes = ['00', '15', '30', '45']

  return hours.flatMap((h) => minutes.map((m) => `${h}:${m}`))
}

const hourMinuteMask = [
  {
    mask: '0:@0',
    definitions: {
      '@': /[0-5]/
    }
  },
  {
    mask: 'HH:MM',
    blocks: {
      HH: {
        mask: IMask.MaskedRange,
        from: 0,
        to: 23
      },
      MM: {
        mask: IMask.MaskedRange,
        from: 0,
        to: 59
      }
    }
  }
]

type TimePickerProps = Omit<MaskedSelectProps, 'options' | 'mask'>

export const TimePicker = ({
  name,
  label,
  value,
  onChange,
  sx,
  disabled,
  required
}: TimePickerProps) => {
  return (
    <MaskedSelect
      options={timeOptions()}
      name={name}
      label={label}
      value={value}
      onChange={onChange}
      placeholder=" "
      sx={sx}
      mask={hourMinuteMask}
      disabled={disabled}
      required={required}
    />
  )
}
