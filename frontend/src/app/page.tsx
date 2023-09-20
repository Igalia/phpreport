import { AuthorizedPage } from '@/app/auth/AuthorizedPage'

export default function Home() {
  return (
    <AuthorizedPage>
      <div>Testing auth</div>
    </AuthorizedPage>
  )
}
