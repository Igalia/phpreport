import { Dismiss12Regular } from '@fluentui/react-icons'
import Button from '@mui/joy/Button'
import { styled } from '@mui/joy'

type ClearInputProps = {
  clearInput: () => void
  disabled?: boolean
}

export const ClearInput = ({ clearInput, disabled }: ClearInputProps) => {
  return (
    <ClearInputWrapper
      tabIndex={-1}
      aria-label="clear input"
      onClick={() => {
        clearInput()
      }}
      disabled={disabled}
    >
      <Dismiss12Regular color="#404040" />
    </ClearInputWrapper>
  )
}

const ClearInputWrapper = styled(Button)`
  background: #fff;
  padding: 4px 8px;

  &:hover {
    background: #e0e0e0;
  }
`
