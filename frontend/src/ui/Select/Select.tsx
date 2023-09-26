import * as React from 'react'
import JoySelect from '@mui/joy/Select'
import Option from '@mui/joy/Option'
import Box from '@mui/joy/Box'
import { styled } from '@mui/joy/styles'
import { SxProps } from '@mui/joy/styles/types'

type Option = {
  value: string
  label: string
}

type SelectProps = {
  options: Array<Option>
  defaultValue: string
  onChange?: (field: string, value: string | null) => void
  name: string
  label: string
  value?: string
  sx?: SxProps
}

export const Select = ({
  options,
  defaultValue = '',
  sx,
  onChange,
  name,
  value,
  label
}: SelectProps) => {
  const selectButtonId = `select-button-${name}`
  const selectLabelId = `select-label-${name}`

  return (
    <Box sx={{ position: 'relative' }}>
      <StyledLabel htmlFor={selectButtonId} id={selectLabelId}>
        {label}
      </StyledLabel>
      <JoySelect
        defaultValue={defaultValue}
        onChange={(_, newValue) => {
          if (onChange) {
            onChange(name, newValue)
          }
        }}
        name={name}
        value={value}
        sx={sx}
        slotProps={{
          button: {
            id: selectButtonId,
            'aria-labelledby': `${selectLabelId} ${selectButtonId}`,
            sx: {
              paddingTop: '1em'
            }
          }
        }}
      >
        {options.map((option) => {
          return (
            <Option key={option.value} value={option.value}>
              {option.label}
            </Option>
          )
        })}
      </JoySelect>
    </Box>
  )
}

const StyledLabel = styled('label')(({ theme }) => ({
  position: 'absolute',
  lineHeight: 1,
  color: theme.vars.palette.text.tertiary,
  fontWeight: theme.vars.fontWeight.md,
  transition: 'all 150ms cubic-bezier(0.4, 0, 0.2, 1)',
  top: '0.5rem',
  left: '0.75rem',
  fontSize: '0.75rem',
  zIndex: 1
}))
