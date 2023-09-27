import * as React from 'react'
import Autocomplete from '@mui/joy/Autocomplete'
import Box from '@mui/joy/Box'
import { styled } from '@mui/joy/styles'
import { AutocompleteProps } from '@mui/joy/Autocomplete'

type Option = {
  value: string
  label: string
}

type SelectProps = {
  label: string
  value?: string
} & Omit<AutocompleteProps<Option, undefined, undefined, undefined>, 'value'>

export const Select = ({ options, sx, onChange, name, value, label, loading }: SelectProps) => {
  const selectButtonId = `select-button-${name}`
  const selectLabelId = `select-label-${name}`

  return (
    <Box sx={{ position: 'relative' }}>
      <StyledLabel htmlFor={selectButtonId} id={selectLabelId}>
        {label}
      </StyledLabel>
      <Autocomplete<Option>
        onChange={onChange}
        name={name}
        value={options.find((option) => option.value === value) || null}
        sx={sx}
        options={options}
        isOptionEqualToValue={(option, value) => option.value === value.value}
        slotProps={{ input: { sx: { pt: '10px' } } }}
        loading={loading}
      ></Autocomplete>
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
