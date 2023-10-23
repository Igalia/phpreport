import * as React from 'react'
import { StyledLabel, StyledInput } from './styles'
import JoyInput from '@mui/joy/Input'
import { SxProps } from '@mui/joy/styles/types'

type InputProps = JSX.IntrinsicElements['input'] & {
  label: string
  list?: string
  sx?: SxProps
  endDecorator?: React.ReactNode
}

const InnerInput = React.forwardRef<HTMLInputElement, InputProps>(function InnerInput(
  { label, ...props },
  ref
) {
  const id = React.useId()
  return (
    <React.Fragment>
      <StyledInput {...props} ref={ref} id={id} />
      <StyledLabel htmlFor={id}>{label}</StyledLabel>
    </React.Fragment>
  )
})

export const Input = ({
  sx,
  label,
  placeholder,
  onChange,
  name,
  value,
  list,
  endDecorator,
  required,
  disabled
}: InputProps) => {
  return (
    <JoyInput
      onChange={onChange}
      slots={{ input: InnerInput }}
      slotProps={{ input: { placeholder, label, name, value, list, required, disabled } }}
      sx={{
        '--Input-minHeight': '56px',
        '--Input-radius': '6px',
        ...sx
      }}
      endDecorator={endDecorator}
    />
  )
}
