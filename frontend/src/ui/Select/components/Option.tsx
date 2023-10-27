import React from 'react'
import Typography from '@mui/joy/Typography'
import { styled } from '@mui/joy'

type OptionsProp = {
  label: string
  selectOption: () => void
  selected: boolean
  key: string
  id: string
}

export const NoResult = () => (
  <li>
    <OptionText>No results</OptionText>
  </li>
)

export const Option = ({ label, selectOption, selected, id }: OptionsProp) => {
  return (
    <li id={id} onClick={selectOption} role="option" aria-selected={selected}>
      <OptionText sx={{ background: selected ? '#efeff4' : '#fff' }}>{label}</OptionText>
    </li>
  )
}

const OptionText = styled(Typography)`
  padding: 8px;

  &:hover {
    background: #efeff4;
  }
`
