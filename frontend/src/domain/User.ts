type Roles = Array<string>

export type User = {
  id: number
  username: string
  email: string
  firstName: string
  lastName: string
  roles: Roles
}
