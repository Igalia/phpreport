import { AuthorizedPage } from '@/app/auth/AuthorizedPage'

export default function Home() {
  return (
    <AuthorizedPage>
      <main>Testing auth</main>
    </AuthorizedPage>
  )
}
