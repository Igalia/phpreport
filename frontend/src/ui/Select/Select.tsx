import * as React from 'react'
import { Input } from '@/ui/Input/Input'
import { SxProps } from '@mui/joy/styles/types'
import { BaseSelect, Option } from './BaseSelect'

type SelectProps = JSX.IntrinsicElements['input'] & {
  sx?: SxProps
  options: Array<Option>
  label: string
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
    <BaseSelect options={options} name={name}>
      <Input
        list={`${name}-list`}
        placeholder={placeholder}
        onChange={onChange}
        value={value}
        name={name}
        sx={sx}
        label={label}
        disabled={disabled}
        required={required}
      />
    </BaseSelect>
  )
}
