import { useContext } from 'react'
import { CreateTaskFormContext } from '../providers/CreateTaskFormProvider'

export const useCreateTaskForm = () => {
  return useContext(CreateTaskFormContext)
}
