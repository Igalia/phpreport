import { useEffect, useState, useMemo, createContext } from 'react'

type Alert = {
  showAlert: boolean
  message: string
  type?: 'warning' | 'success'
}

type AlertContext = {
  submitAlert: Alert
  closeAlert: () => void
  showSuccess: (message: string) => void
  showError: (message: string) => void
}

export const AlertContext = createContext({} as AlertContext)

export const AlertProvider = ({ children }: { children: React.ReactNode }) => {
  const [submitAlert, setSubmitAlert] = useState<Alert>({
    showAlert: false,
    message: 'test'
  })

  const closeAlert = () =>
    setSubmitAlert({
      showAlert: false,
      message: ''
    })

  const showSuccess = (message: string) =>
    setSubmitAlert({ showAlert: true, message, type: 'success' })

  const showError = (message: string) =>
    setSubmitAlert({ showAlert: true, message, type: 'warning' })

  useEffect(() => {
    let timeoutId: NodeJS.Timeout | undefined
    if (submitAlert.showAlert) {
      timeoutId = setTimeout(closeAlert, 15000)
    }

    return () => clearTimeout(timeoutId)
  }, [submitAlert])

  const value = useMemo(
    () => ({
      submitAlert,
      showSuccess,
      showError,
      closeAlert
    }),
    [submitAlert]
  )

  return <AlertContext.Provider value={value}>{children}</AlertContext.Provider>
}
