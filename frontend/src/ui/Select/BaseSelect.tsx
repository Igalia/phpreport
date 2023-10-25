'use client'

import React, { useState } from 'react'
import Box from '@mui/joy/Box'
import { styled } from '@mui/joy'
import { ClearInput } from './components/ClearInput'
import { Option, NoResult } from './components/Option'
import { getDisplayValue, autoCompleteMatch } from './select-utils'
import { Options } from './types'

type RenderInputProps = React.InputHTMLAttributes<HTMLInputElement> & {
  endDecorator?: React.ReactNode
}

type BaseSelectProps = {
  options: Options
  name?: string
  renderInput: (props: RenderInputProps) => React.ReactNode
  value: string
  onChange?: (value: string) => void
}

type OptionsProps = {
  options: Options
  name?: string
  value: string
  selectOption: (value: string) => void
  activeIndex: number
}

const Options = ({ options, selectOption, name, value, activeIndex }: OptionsProps) => {
  if (options.length === 0) {
    return <NoResult></NoResult>
  }

  return options.map((option, index) => {
    let selected: boolean
    let label: string
    let key: string
    let newValue: string

    if (typeof option === 'string') {
      selected = option === value
      label = option
      key = `${name}-${option}`
      newValue = option
    } else {
      selected = option.value === value
      label = option.label
      key = option.value
      newValue = option.value
    }

    return (
      <Option
        selected={selected}
        label={label}
        key={key}
        selectOption={() => selectOption(newValue)}
        isActive={index === activeIndex}
      />
    )
  })
}

export const BaseSelect = ({ options, name, renderInput, value, onChange }: BaseSelectProps) => {
  const [open, setOpen] = useState(false)
  const [activeOption, setActiveOption] = useState(-1)

  const selectId = `${name}-select-dropdown`

  const displayValue = getDisplayValue(value, options)
  const filteredOptions = autoCompleteMatch(displayValue, options)

  const navigateList = (e: React.KeyboardEvent) => {
    if (e.key === 'ArrowDown' && activeOption < filteredOptions.length - 1) {
      e.preventDefault()

      setActiveOption((prevState) => prevState + 1)
    }

    if (e.key === 'ArrowUp' && filteredOptions.length > 0) {
      e.preventDefault()

      setActiveOption((prevState) => prevState - 1)
    }
  }

  return (
    <Box onKeyDown={navigateList} onBlur={() => setOpen(false)} sx={{ position: 'relative' }}>
      {renderInput({
        value: displayValue,
        role: 'combobox',
        name,
        'aria-autocomplete': 'list',
        'aria-haspopup': 'listbox',
        'aria-expanded': open,
        'aria-controls': selectId,
        'aria-labelledby': 'select input',
        onKeyDown: (e) => {
          if (onChange && e.key === 'Tab') {
            if (value.length > 0 && filteredOptions.length > 0) {
              const nextOption = filteredOptions[0]
              const newValue = typeof nextOption === 'string' ? nextOption : nextOption.value

              onChange(newValue)
            }

            setOpen(false)
          }
        },
        onFocus: () => setOpen(true),
        endDecorator: (
          <>
            <SelectDropdown
              sx={{
                opacity: open ? 100 : 0,
                visibility: open ? 'visible' : 'hidden'
              }}
              component="ul"
              role="listbox"
              id={selectId}
              tabIndex={filteredOptions.length > 0 ? 0 : undefined}
              onFocus={() => setOpen(true)}
              onBlur={() => setOpen(false)}
            >
              <Options
                options={filteredOptions as Options}
                name={name}
                value={value}
                activeIndex={activeOption}
                selectOption={(value) => {
                  setOpen(false)
                  if (onChange) {
                    setActiveOption(-1)
                    onChange(value)
                  }
                }}
              />
            </SelectDropdown>
            {value.length > 0 && (
              <ClearInput
                clearInput={() => {
                  if (onChange) {
                    onChange('')
                    setActiveOption(-1)
                    setOpen(false)
                  }
                }}
              />
            )}
          </>
        )
      })}
    </Box>
  )
}

const SelectDropdown = styled(Box)`
  position: absolute;
  top: 55px;
  right: 0;
  z-index: 1;
  list-style: none;
  width: 100%;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  background-color: #fff;
  border: 1px solid #caced1;
  border-radius: 8px;
  padding: 10px 0;
  margin-top: 5px;
  max-height: 200px;
  overflow-y: auto;
  transition: 0.2s ease;
  cursor: pointer;

  &:focus-within {
    box-shadow: 0 10px 25px rgba(94, 108, 233, 0.6);
  }

  &::-webkit-scrollbar {
    width: 7px;
  }
  &::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 25px;
  }

  &::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 25px;
  }
`
