import { useSession } from 'next-auth/react'
import { useEffect } from 'react'

const FIVE_MINUTES_IN_MILISSECONDS = 1000 * 60 * 5

export const RefreshSession = ({ children }: { children: React.ReactNode }) => {
  // update() triggers the jwt callback on next-auth/route.ts
  const { update, data } = useSession({
    required: true
  })

  // Refresh the session on a time interval
  useEffect(() => {
    // TIP: You can also use `navigator.onLine` and some extra event handlers
    // to check if the user is online and only update the session if they are.
    // https://developer.mozilla.org/en-US/docs/Web/API/Navigator/onLine
    const interval = setInterval(() => update(), FIVE_MINUTES_IN_MILISSECONDS)
    return () => clearInterval(interval)
  }, [update])

  // Refresh the session on window re-focus if the token is expired
  useEffect(() => {
    const visibilityHandler = () => {
      if (
        document.visibilityState === 'visible' &&
        data?.accessTokenExpires &&
        Date.now() > data.accessTokenExpires
      ) {
        update()
      }
    }

    window.addEventListener('visibilitychange', visibilityHandler, false)
    return () => window.removeEventListener('visibilitychange', visibilityHandler, false)
  }, [data, update])

  return children
}
