export type Option =
  | string
  | {
      value: string
      label: string
    }

type BaseSelectProps = {
  options: Array<Option>
  name?: string
  children: React.ReactNode
}

export const BaseSelect = ({ options, name, children }: BaseSelectProps) => {
  return (
    <div>
      {children}
      <datalist id={`${name}-list`}>
        {options.map((option) => {
          if (typeof option === 'string') {
            return <option key={`${name}-${option}`}>{option}</option>
          } else {
            return <option key={option.value}>{option.label}</option>
          }
        })}
      </datalist>
    </div>
  )
}
