import * as React from 'react'
import { StyledLabel, StyledInput } from './styles'
import JoyInput from '@mui/joy/Input'
import { SxProps } from '@mui/joy/styles/types'

type InputProps = React.InputHTMLAttributes<HTMLInputElement> & {
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

export const Input = ({ sx, onChange, endDecorator, ...inputProps }: InputProps) => {
  return (
    <JoyInput
      onChange={onChange}
      slots={{ input: InnerInput }}
      slotProps={{ input: inputProps }}
      sx={{
        '--Input-minHeight': '56px',
        '--Input-radius': '6px',
        ...sx
      }}
      endDecorator={endDecorator}
    />
  )
}
