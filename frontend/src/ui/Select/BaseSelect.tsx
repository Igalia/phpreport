'use client'

import React, { useState, useRef, useEffect } from 'react'
import Box from '@mui/joy/Box'
import Typography from '@mui/joy/Typography'
import Button from '@mui/joy/Button'
import { styled } from '@mui/joy'
import { Dismiss12Regular } from '@fluentui/react-icons'

type Option = {
  value: string
  label: string
}

export type Options = Array<string> | Array<Option>

type RenderInputProps = React.InputHTMLAttributes<HTMLInputElement> & {
  endDecorator: React.ReactNode
}

type BaseSelectProps = {
  options: Options
  name?: string
  renderInput: (props: RenderInputProps) => React.ReactNode
  value: string
  onChange?: (value: string) => void
}

type OptionsProp = {
  label: string
  selectOption: () => void
  isActive: boolean
  selected: boolean
  key: string
}

type OptionsProps = {
  options: Options
  name?: string
  value: string
  selectOption: (value: string) => void
  activeIndex: number
}

type ClearInputProps = {
  clearInput: () => void
}

const getDisplayValue = (value: string, options: Options) => {
  if (options[0] === 'string') {
    return value
  }

  return (options as Array<Option>).find((option) => option.value === value)?.label || value
}

const autoCompleteMatch = (value: string, options: Options) => {
  const regex = new RegExp('^' + value, 'i')

  if (value.length === 0) {
    return options
  }

  return options.filter((option) => {
    const optionLabel = typeof option === 'string' ? option : option.label

    return optionLabel.match(regex)
  })
}

const ClearInput = ({ clearInput }: ClearInputProps) => {
  return (
    <ClearInputWrapper onClick={clearInput}>
      <Dismiss12Regular color="#404040" />
    </ClearInputWrapper>
  )
}

const Option = ({ label, selectOption, isActive, selected, key }: OptionsProp) => {
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
      key={key}
    >
      <OptionText sx={{ background: isActive ? '#efeff4' : '#fff' }}>{label}</OptionText>
    </li>
  )
}

const Options = ({ options, selectOption, name, value, activeIndex }: OptionsProps) => {
  if (options.length === 0) {
    return (
      <li>
        <OptionText>No results</OptionText>
      </li>
    )
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

  const handleKeyDown = (e: React.KeyboardEvent) => {
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
    <Box onKeyDown={handleKeyDown} onBlur={() => setOpen(false)} sx={{ position: 'relative' }}>
      {renderInput({
        value: displayValue,
        role: 'combobox',
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
              tabIndex={0}
              onFocus={() => setOpen(true)}
              onBlur={() => setOpen(false)}
            >
              <Options
                options={filteredOptions as Options}
                name={name}
                value={value}
                activeIndex={activeOption}
                selectOption={(value) => {
                  if (onChange) {
                    setActiveOption(-1)
                    onChange(value)
                  }
                  setOpen(false)
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
        ),
        'aria-autocomplete': 'list',
        'aria-haspopup': 'listbox',
        'aria-expanded': open,
        'aria-controls': selectId,
        'aria-labelledby': 'select input',
        onKeyDown: (e) => {
          if (onChange && e.key === 'Tab') {
            if (value.length > 0) {
              const nextOption = filteredOptions[0]
              const newValue = typeof nextOption === 'string' ? nextOption : nextOption.value

              onChange(newValue)
            }

            setOpen(false)
          }
        }
      })}
    </Box>
  )
}

const OptionText = styled(Typography)`
  padding: 8px;

  &:hover {
    background: #efeff4;
  }
`

const ClearInputWrapper = styled(Button)`
  background: #fff;
  padding: 4px 8px;

  &:hover {
    background: #e0e0e0;
  }
`

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
