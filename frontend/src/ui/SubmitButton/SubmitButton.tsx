import { useFormStatus } from 'react-dom'
import { Button } from '@mui/joy'
import { PropsWithChildren } from 'react'
import { SxProps } from '@mui/joy/styles/types'

type SubmitButtonProps = {
  sx?: SxProps
}

export const SubmitButton = ({ children, sx }: PropsWithChildren<SubmitButtonProps>) => {
  const { pending } = useFormStatus()

  return (
    <Button sx={sx} type="submit" disabled={pending} aria-disabled={pending}>
      {children}
    </Button>
  )
}
