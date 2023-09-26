import * as React from 'react'
import { styled } from '@mui/joy/styles'
import JoyInput from '@mui/joy/Input'

type InputProps = {
  label: string
  placeholder: string
  onChange: (field: string, value: string | null) => void
  name: string
  value: string
}

type InnerInputProps = JSX.IntrinsicElements['input'] & InputProps

const InnerInput = React.forwardRef<HTMLInputElement, InnerInputProps>(function InnerInput(
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

export const Input = ({ label, placeholder, onChange, name, value }: InputProps) => {
  return (
    <JoyInput
      onChange={(e) => onChange(name, e.target.value)}
      slots={{ input: InnerInput }}
      slotProps={{ input: { placeholder, label, name, value } }}
      sx={{
        '--Input-minHeight': '56px',
        '--Input-radius': '6px'
      }}
    />
  )
}

const StyledLabel = styled('label')(({ theme }) => ({
  position: 'absolute',
  lineHeight: 1,
  top: 'calc((var(--Input-minHeight) - 1em) / 2)',
  color: theme.vars.palette.text.tertiary,
  fontWeight: theme.vars.fontWeight.md,
  transition: 'all 150ms cubic-bezier(0.4, 0, 0.2, 1)'
}))

const StyledInput = styled('input')({
  border: 'none', // remove the native input border
  minWidth: 0, // remove the native input width
  outline: 0, // remove the native input outline
  padding: 0, // remove the native input padding
  paddingTop: '1em',
  flex: 1,
  color: 'inherit',
  backgroundColor: 'transparent',
  fontFamily: 'inherit',
  fontSize: 'inherit',
  fontStyle: 'inherit',
  fontWeight: 'inherit',
  lineHeight: 'inherit',
  textOverflow: 'ellipsis',
  '&::placeholder': {
    opacity: 0,
    transition: '0.1s ease-out'
  },
  '&:focus::placeholder': {
    opacity: 1
  },
  '&:focus ~ label, &:not(:placeholder-shown) ~ label, &:-webkit-autofill ~ label': {
    top: '0.5rem',
    fontSize: '0.75rem'
  },
  '&:focus ~ label': {
    color: 'var(--Input-focusedHighlight)'
  },
  '&:-webkit-autofill': {
    alignSelf: 'stretch' // to fill the height of the root slot
  },
  '&:-webkit-autofill:not(* + &)': {
    marginInlineStart: 'calc(-1 * var(--Input-paddingInline))',
    paddingInlineStart: 'var(--Input-paddingInline)',
    borderTopLeftRadius: 'calc(var(--Input-radius) - var(--variant-borderWidth, 0px))',
    borderBottomLeftRadius: 'calc(var(--Input-radius) - var(--variant-borderWidth, 0px))'
  }
})
