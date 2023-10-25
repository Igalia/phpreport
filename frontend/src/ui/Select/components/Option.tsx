import React, { useRef, useEffect } from 'react'
import Typography from '@mui/joy/Typography'
import { styled } from '@mui/joy'

type OptionsProp = {
  label: string
  selectOption: () => void
  isActive: boolean
  selected: boolean
  key: string
}

export const NoResult = () => (
  <li>
    <OptionText>No results</OptionText>
  </li>
)

export const Option = ({ label, selectOption, isActive, selected }: OptionsProp) => {
  const optionRef = useRef<HTMLLIElement>(null)

  useEffect(() => {
    if (isActive) {
      optionRef.current?.focus()
    }
  }, [isActive])

  return (
    <li
      tabIndex={-1}
      onClick={selectOption}
      onKeyDown={(e) => {
        if (isActive && e.key === 'Tab') {
          selectOption()
        }
      }}
      ref={optionRef}
      role="option"
      aria-selected={selected}
    >
      <OptionText sx={{ background: isActive ? '#efeff4' : '#fff' }}>{label}</OptionText>
    </li>
  )
}

const OptionText = styled(Typography)`
  padding: 8px;

  &:hover {
    background: #efeff4;
  }
`
