type Roles = Array<string>

export type User = {
  id: number
  username: string
  email: string
  firstName: string
  lastName: string
  roles: Roles
  capacities: Array<UserCapacity>
}

type UserCapacity = {
  capacity: number
  start: string
  end: string
  isCurrent: boolean
}
