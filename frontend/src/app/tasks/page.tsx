import { AuthorizedPage } from '@/app/auth/AuthorizedPage'
import { TaskForm } from './TaskForm'

export default function Tasks() {
  return (
    <AuthorizedPage>
      <TaskForm />
    </AuthorizedPage>
  )
}
