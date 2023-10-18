import { useContext } from 'react'
import { AlertContext } from './AlertProvider'

export const useAlert = () => {
  return useContext(AlertContext)
}
