'use client'

import React, { useState, useEffect } from 'react'
import Box from '@mui/joy/Box'
import { styled } from '@mui/joy'
import { ClearInput } from './components/ClearInput'
import { Option } from './components/Option'
import { getDisplayValue, autoCompleteMatch } from './select-utils'
import { Options } from './types'

const NO_OPTION_SELECTED = -1

type RenderInputProps = Omit<React.InputHTMLAttributes<HTMLInputElement>, 'onChange'> & {
  endDecorator?: React.ReactNode
  onChange: (value: string) => void
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
  selectOption: (value: string) => void
  activeIndex: number
}

const Options = ({ options, selectOption, name, activeIndex }: OptionsProps) => {
  return options.map((option, index) => {
    let label: string
    let key: string
    let newValue: string

    if (typeof option === 'string') {
      label = option
      key = `${name}-${option}`
      newValue = option
    } else {
      label = option.label
      key = option.value
      newValue = option.value
    }

    return (
      <Option
        label={label}
        key={key}
        selectOption={() => selectOption(newValue)}
        selected={index === activeIndex}
        id={`${name}-${index}`}
      />
    )
  })
}

export const BaseSelect = ({ options, name, renderInput, value, onChange }: BaseSelectProps) => {
  const [open, setOpen] = useState(false)
  const [activeOption, setActiveOption] = useState(NO_OPTION_SELECTED)
  const [displayValue, setDisplayValue] = useState(getDisplayValue(value, options))

  const selectId = `${name}-select-dropdown`
  const filteredOptions = autoCompleteMatch(displayValue, options)

  const handleChange = (newValue: string) => {
    if (onChange) {
      onChange(newValue)
    }

    setDisplayValue(getDisplayValue(newValue, options))
  }

  useEffect(() => {
    if (filteredOptions.length === 0) {
      setOpen(false)
    }
  }, [filteredOptions])

  const selectedOption = () => {
    const selected = filteredOptions[activeOption]

    return typeof selected === 'string' ? selected : selected.value
  }

  const closeDropdown = () => {
    setOpen(false)
    setActiveOption(NO_OPTION_SELECTED)
  }

  return (
    <Box sx={{ position: 'relative' }}>
      {renderInput({
        value: displayValue,
        role: 'combobox',
        name,
        autoComplete: 'off',
        'aria-autocomplete': 'list',
        'aria-activedescendant': `${name}-${activeOption}`,
        'aria-haspopup': 'listbox',
        'aria-expanded': open,
        'aria-controls': selectId,
        'aria-labelledby': 'select input',
        onClick: () => setOpen(true),
        onBlur: () => closeDropdown(),
        onChange: (newValue) => {
          if (!open && newValue.length > 0 && !(filteredOptions.length === 1)) {
            setOpen(true)
          }

          handleChange(newValue)
          setActiveOption(NO_OPTION_SELECTED)
        },
        onKeyDown: (e) => {
          if (e.key === 'ArrowDown' && e.altKey) {
            if (!open) {
              setOpen(true)
            }
          }

          const lastElement = filteredOptions.length - 1
          const firstElement = 0

          switch (e.key) {
            case 'Tab':
              if (value.length > 0 && filteredOptions.length > 0) {
                const optionIndex = activeOption === NO_OPTION_SELECTED ? 0 : activeOption
                const nextOption = filteredOptions[optionIndex]
                const newValue = typeof nextOption === 'string' ? nextOption : nextOption.value

                handleChange(newValue)
              }
              break

            case 'Enter':
              if (open) {
                e.preventDefault()
              }

              if (activeOption !== NO_OPTION_SELECTED) {
                handleChange(selectedOption())
              }

              closeDropdown()
              break
            case 'Escape':
              if (open) {
                closeDropdown()
              }

              handleChange('')
              break
            case 'ArrowLeft':
            case 'ArrowRight':
            case 'Home':
            case 'End':
              setActiveOption(NO_OPTION_SELECTED)
              break
            case 'ArrowDown':
              if (!open) {
                setOpen(true)
              }

              if (activeOption === NO_OPTION_SELECTED || activeOption === lastElement) {
                e.preventDefault()
                setActiveOption(firstElement)
              } else {
                e.preventDefault()
                setActiveOption((prevState) => prevState + 1)
              }
              break
            case 'ArrowUp':
              if (!open) {
                setOpen(true)
              }

              if (activeOption === NO_OPTION_SELECTED || activeOption === firstElement) {
                e.preventDefault()
                setActiveOption(lastElement)
              } else {
                e.preventDefault()
                setActiveOption((prevState) => prevState - 1)
              }
              break
          }
        },
        endDecorator: (
          <>
            {value.length > 0 && (
              <ClearInput
                clearInput={() => {
                  handleChange('')
                  closeDropdown()
                }}
              />
            )}
          </>
        )
      })}
      <SelectDropdown
        sx={{
          opacity: open ? 100 : 0,
          visibility: open ? 'visible' : 'hidden'
        }}
        component="ul"
        role="listbox"
        data-testid={`${name}-dropdown`}
        id={selectId}
        tabIndex={-1}
      >
        <Options
          options={filteredOptions as Options}
          name={name}
          activeIndex={activeOption}
          selectOption={(value) => handleChange(value)}
        />
      </SelectDropdown>
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
