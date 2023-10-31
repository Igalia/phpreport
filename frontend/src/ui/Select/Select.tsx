import * as React from 'react'
import { Input } from '@/ui/Input/Input'
import { SxProps } from '@mui/joy/styles/types'
import { BaseSelect } from './BaseSelect'
import { Options } from './types'

type SelectProps = Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange'> & {
  sx?: SxProps
  options: Options
  label: string
  value: string
  onChange?: (value: string) => void
}

export const Select = ({
  sx,
  value,
  onChange,
  options,
  name,
  label,
  placeholder,
  disabled,
  required
}: SelectProps) => {
  return (
    <BaseSelect
      options={options}
      name={name}
      value={value}
      onChange={onChange}
      disabled={disabled}
      renderInput={(props) => (
        <Input
          {...props}
          placeholder={placeholder}
          onChange={(e) => {
            if (props.onChange) {
              props.onChange(e.target.value)
            }
          }}
          name={name}
          sx={sx}
          label={label}
          required={required}
        />
      )}
    />
  )
}
