import React, { useRef, useEffect } from 'react'
import Typography from '@mui/joy/Typography'
import { styled } from '@mui/joy'

type OptionsProp = {
  label: string
  selectOption: () => void
  selected: boolean
  key: string
  id: string
}

export const Option = ({ label, selectOption, selected, id }: OptionsProp) => {
  const liRef = useRef<HTMLLIElement>(null)

  useEffect(() => {
    if (selected) {
      liRef.current?.scrollIntoView({ behavior: 'smooth', block: 'nearest' })
    }
  }, [selected])

  return (
    <li id={id} ref={liRef} onClick={selectOption} role="option" aria-selected={selected}>
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
