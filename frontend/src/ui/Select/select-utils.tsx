import { Options, Option } from './types'

export const getDisplayValue = (value: string, options: Options) => {
  if (options[0] === 'string') {
    return value
  }

  return (options as Array<Option>).find((option) => option.value === value)?.label || value
}

export const autoCompleteMatch = (value: string, options: Options) => {
  const regex = new RegExp('^' + value.replace(':', ''), 'i')

  if (value.length === 0) {
    return options
  }

  return options.filter((option) => {
    const optionLabel = typeof option === 'string' ? option : option.label

    return optionLabel.replace(':', '').match(regex)
  })
}
