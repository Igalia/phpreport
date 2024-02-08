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
  const reactId = React.useId()
  const id = props.id || reactId
  return (
    <React.Fragment>
      <StyledInput id={id} ref={ref} {...props} />
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
        '--Input-minHeight': '40px',
        '--Input-radius': '6px',
        ...sx
      }}
      endDecorator={endDecorator}
    />
  )
}
