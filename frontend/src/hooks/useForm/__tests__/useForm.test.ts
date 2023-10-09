import { renderHook, act } from '@testing-library/react'
import { useForm } from '../useForm'

describe('useForm', () => {
  it('accepts form initial values', () => {
    const { result } = renderHook(() => useForm({ initialValues: { name: 'John', age: 0 } }))

    expect(result.current.formState).toEqual({ name: 'John', age: 0 })
  })

  it('changes the values', () => {
    const { result } = renderHook(() => useForm({ initialValues: { name: '', age: 0 } }))

    act(() => {
      result.current.handleChange('name', 'John')
      result.current.handleChange('age', 51)
    })

    expect(result.current.formState).toEqual({ name: 'John', age: 51 })
  })

  it('resets the form values', () => {
    const { result } = renderHook(() => useForm({ initialValues: { name: '', age: 0 } }))

    act(() => {
      result.current.handleChange('name', 'John')
      result.current.handleChange('age', 51)
    })

    act(() => {
      result.current.resetForm()
    })

    expect(result.current.formState).toEqual({ name: '', age: 0 })
  })
})
