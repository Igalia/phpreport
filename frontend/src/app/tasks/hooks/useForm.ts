import { useState } from 'react'

type UseFormProps<T> = {
  initialValues: T
}

export const useForm = <T>({ initialValues }: UseFormProps<T>) => {
  const [formState, setFormState] = useState(initialValues)

  const handleChange = (field: string, newValue: string | null) => {
    setFormState((prevFormState) => ({ ...prevFormState, [field]: newValue }))
  }

  const resetForm = () => setFormState(initialValues)

  return { handleChange, formState, resetForm }
}
