import { MaskedInput, Mask } from '@/ui/Input/MaskedInput'
import { SxProps } from '@mui/joy/styles/types'
import { BaseSelect, Option } from './BaseSelect'

export type MaskedSelectProps = Omit<JSX.IntrinsicElements['input'], 'onChange'> & {
  sx?: SxProps
  options: Array<Option>
  label: string
  mask: Mask
  onChange: (option: string) => void
}

export const MaskedSelect = ({
  sx,
  value,
  onChange,
  options,
  name,
  label,
  placeholder,
  disabled,
  required,
  mask
}: MaskedSelectProps) => {
  return (
    <BaseSelect options={options} name={name}>
      <MaskedInput
        list={`${name}-list`}
        placeholder={placeholder}
        onAccept={onChange}
        value={value}
        name={name}
        sx={sx}
        label={label}
        disabled={disabled}
        required={required}
        mask={mask}
      />
    </BaseSelect>
  )
}
