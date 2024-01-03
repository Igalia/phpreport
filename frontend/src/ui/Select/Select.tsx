import * as React from 'react'

import Autocomplete from '@mui/joy/Autocomplete'
import Box from '@mui/joy/Box'
import { styled } from '@mui/joy/styles'
import { AutocompleteProps } from '@mui/joy/Autocomplete'

type SelectProps<T> = {
  label: string
} & AutocompleteProps<T, undefined, undefined, undefined>

export const Select = <T,>({
  options,
  sx,
  onChange,
  name,
  value,
  label,
  loading,
  getOptionLabel,
  disabled,
  required,
  getOptionKey
}: SelectProps<T>) => {
  const selectButtonId = `select-button-${name}`
  const selectLabelId = `select-label-${name}`

  return (
    <Box sx={{ position: 'relative', zIndex: 0 }}>
      <StyledLabel htmlFor={selectButtonId} id={selectLabelId}>
        {label}
      </StyledLabel>
      <Autocomplete
        id={selectButtonId}
        onChange={onChange}
        name={name}
        value={value}
        sx={sx}
        options={options}
        autoSelect
        autoHighlight
        slotProps={{ input: { sx: { pt: '10px' } } }}
        loading={loading}
        getOptionLabel={getOptionLabel}
        disabled={disabled}
        required={required}
        getOptionKey={getOptionKey}
      />
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
