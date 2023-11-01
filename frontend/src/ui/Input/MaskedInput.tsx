import * as React from 'react'
import JoyInput from '@mui/joy/Input'
import { SxProps } from '@mui/joy/styles/types'
import { IMaskMixin, IMaskInput } from 'react-imask'
import { StyledLabel, StyledInput } from './styles'

export type Mask = React.ComponentProps<typeof IMaskInput>['mask']

type InputProps = React.InputHTMLAttributes<HTMLInputElement> & {
  label: string
  onAccept?: (value: string) => void
  sx?: SxProps
  endDecorator?: React.ReactNode
  mask: Mask
}

type InnerInputProps = React.InputHTMLAttributes<HTMLInputElement> & InputProps

const MixinInput = IMaskMixin(({ inputRef, ...props }) => {
  return <StyledInput ref={inputRef} {...props} />
})

const MaskedInputAdapter = React.forwardRef<HTMLElement, InnerInputProps>(function MaskedInput(
  { onAccept, label, ...props },
  ref
) {
  const id = React.useId()

  return (
    <>
      <MixinInput {...props} inputRef={ref} id={id} onAccept={onAccept} />
      <StyledLabel htmlFor={id}>{label}</StyledLabel>
    </>
  )
})

export const MaskedInput = ({
  sx,
  label,
  onAccept,
  endDecorator,
  mask,
  ...inputProps
}: InputProps) => {
  return (
    <JoyInput
      slotProps={{
        input: {
          component: MaskedInputAdapter,
          label,
          mask,
          onAccept,
          ...inputProps
        }
      }}
      sx={{
        '--Input-minHeight': '56px',
        '--Input-radius': '6px',
        ...sx
      }}
      endDecorator={endDecorator}
    />
  )
}
