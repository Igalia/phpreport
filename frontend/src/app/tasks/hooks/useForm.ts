import { useState } from 'react'

type UseFormProps<T> = {
  initialValues: T
}

export const useForm = <T>({ initialValues }: UseFormProps<T>) => {
  const [formState, setFormState] = useState(initialValues)

  const handleChange = <F extends keyof T>(field: F, newValue: T[F]) => {
    setFormState((prevFormState) => ({ ...prevFormState, [field]: newValue }))
  }

  const resetForm = () => setFormState(initialValues)

  return { handleChange, formState, resetForm }
}
