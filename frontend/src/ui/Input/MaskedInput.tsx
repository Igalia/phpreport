import * as React from 'react'
import JoyInput from '@mui/joy/Input'
import { SxProps } from '@mui/joy/styles/types'
import { IMaskMixin, IMaskInput } from 'react-imask'
import { StyledLabel, StyledInput } from './styles'

export type Mask = React.ComponentProps<typeof IMaskInput>['mask']

type InputProps = JSX.IntrinsicElements['input'] & {
  label: string
  onAccept?: (value: string) => void
  sx?: SxProps
  endDecorator?: React.ReactNode
  mask: Mask
}

type InnerInputProps = JSX.IntrinsicElements['input'] & InputProps

const MixinInput = IMaskMixin(({ inputRef, ...props }) => <StyledInput ref={inputRef} {...props} />)

const MaskedInput1 = React.forwardRef<HTMLElement, InnerInputProps>(function MaskedInput(
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
  placeholder,
  onAccept,
  name,
  value,
  list,
  endDecorator,
  mask,
  required,
  disabled
}: InputProps) => {
  return (
    <JoyInput
      slotProps={{
        input: {
          component: MaskedInput1,
          placeholder,
          label,
          name,
          value,
          list,
          mask,
          onAccept,
          required,
          disabled
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
