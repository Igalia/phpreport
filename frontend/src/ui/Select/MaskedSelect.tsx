import { MaskedInput, Mask } from '@/ui/Input/MaskedInput'
import { SxProps } from '@mui/joy/styles/types'
import { BaseSelect } from './BaseSelect'
import { Options } from './types'

export type MaskedSelectProps = Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange'> & {
  sx?: SxProps
  options: Options
  label: string
  value: string
  mask: Mask
  onChange?: (value: string) => void
}

export const MaskedSelect = ({
  sx,
  value,
  onChange,
  options,
  name,
  label,
  placeholder,
  disabled,
  required,
  mask
}: MaskedSelectProps) => {
  return (
    <BaseSelect
      options={options}
      name={name}
      value={value}
      onChange={onChange}
      renderInput={({ onChange, ...props }) => (
        <MaskedInput
          {...props}
          placeholder={placeholder}
          onAccept={(value) => {
            if (onChange) {
              onChange(value)
            }
          }}
          name={name}
          sx={sx}
          label={label}
          disabled={disabled}
          required={required}
          mask={mask}
        />
      )}
    />
  )
}
