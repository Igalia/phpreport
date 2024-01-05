import { useCallback, useState, useRef, useEffect } from 'react'

type UseFormProps<T> = {
  initialValues: T
}

export const useForm = <T>({ initialValues }: UseFormProps<T>) => {
  const [formState, setFormState] = useState(initialValues)

  const handleChange = useCallback(<F extends keyof T>(field: F, newValue: T[F]) => {
    setFormState((prevFormState) => ({ ...prevFormState, [field]: newValue }))
  }, [])

  const resetForm = useCallback(() => setFormState(initialValues), [initialValues])

  const formRef = useRef<HTMLFormElement>(null)

  useEffect(() => {
    const handleKeyPress = (event: KeyboardEvent) => {
      if (event.key === 's' && event.ctrlKey && formRef.current?.contains(document.activeElement)) {
        event.preventDefault()
        formRef.current?.requestSubmit()
      }
    }
    document.addEventListener('keydown', handleKeyPress)

    return () => {
      document.removeEventListener('keydown', handleKeyPress)
    }
  }, [])

  return { handleChange, formState, resetForm, setFormState, formRef }
}
