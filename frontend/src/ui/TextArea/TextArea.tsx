import * as React from 'react'
import { TextareaAutosize } from '@mui/base/TextareaAutosize'
import Textarea from '@mui/joy/Textarea'
import { styled } from '@mui/joy/styles'
import { SxProps } from '@mui/joy/styles/types'

type TextAreaProps = {
  label: string
  placeholder: string
  name: string
  sx?: SxProps
  onChange: React.ChangeEventHandler<HTMLTextAreaElement> | undefined
  value: string
}

type InnerTextAreaProps = JSX.IntrinsicElements['textarea'] & Omit<TextAreaProps, 'sx'>

const InnerTextarea = React.forwardRef<HTMLTextAreaElement, InnerTextAreaProps>(
  function InnerTextarea({ label, ...props }, ref) {
    const id = React.useId()
    return (
      <React.Fragment>
        <StyledTextarea minRows={2} {...props} ref={ref} id={id} />
        <StyledLabel htmlFor={id}>{label}</StyledLabel>
      </React.Fragment>
    )
  }
)

export const TextArea = ({ sx = {}, label, placeholder, name, onChange, value }: TextAreaProps) => {
  return (
    <Textarea
      onChange={onChange}
      slots={{ textarea: InnerTextarea }}
      slotProps={{ textarea: { placeholder, label, name } }}
      sx={{ borderRadius: '8px', ...sx }}
      value={value}
    />
  )
}

const StyledTextarea = styled(TextareaAutosize)({
  resize: 'none',
  border: 'none', // remove the native textarea border
  minWidth: 0, // remove the native textarea width
  outline: 0, // remove the native textarea outline
  padding: 0, // remove the native textarea padding
  paddingBlockStart: '1em',
  paddingInlineEnd: `var(--Textarea-paddingInline)`,
  flex: 'auto',
  alignSelf: 'stretch',
  color: 'inherit',
  backgroundColor: 'transparent',
  fontFamily: 'inherit',
  fontSize: 'inherit',
  fontStyle: 'inherit',
  fontWeight: 'inherit',
  lineHeight: 'inherit',
  '&::placeholder': {
    opacity: 0,
    transition: '0.1s ease-out'
  },
  '&:focus::placeholder': {
    opacity: 1
  },
  // specific to TextareaAutosize, cannot use '&:focus ~ label'
  '&:focus + textarea + label, &:not(:placeholder-shown) + textarea + label': {
    top: '0.5rem',
    fontSize: '0.75rem'
  },
  '&:focus + textarea + label': {
    color: 'var(--Textarea-focusedHighlight)'
  }
})

const StyledLabel = styled('label')(({ theme }) => ({
  position: 'absolute',
  lineHeight: 1,
  top: 'calc((var(--Textarea-minHeight) - 1em) / 2)',
  color: theme.vars.palette.text.tertiary,
  fontWeight: theme.vars.fontWeight.md,
  transition: 'all 150ms cubic-bezier(0.4, 0, 0.2, 1)'
}))
