import { useCallback, useState } from 'react'

type UseFormProps<T> = {
  initialValues: T
}

export const useForm = <T>({ initialValues }: UseFormProps<T>) => {
  const [formState, setFormState] = useState(initialValues)

  const handleChange = useCallback(<F extends keyof T>(field: F, newValue: T[F]) => {
    setFormState((prevFormState) => ({ ...prevFormState, [field]: newValue }))
  }, [])

  const resetForm = useCallback(() => setFormState(initialValues), [initialValues])

  return { handleChange, formState, resetForm, setFormState }
}
