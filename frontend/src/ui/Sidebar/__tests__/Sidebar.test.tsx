import { Sidebar } from '../Sidebar'
import { render, screen } from '@/test-utils/test-utils'
import { useSession } from 'next-auth/react'

jest.mock('next-auth/react')

beforeEach(() => {
  ;(useSession as jest.Mock).mockReturnValue({ data: null })
})

describe('Sidebar', () => {
  it('renders the Igalia logo', () => {
    render(<Sidebar />)

    expect(screen.getByRole('img', { name: 'Igalia Logo' })).toBeInTheDocument()
  })

  it('renders the navigation links', () => {
    render(<Sidebar />)

    expect(screen.getByRole('link', { name: 'Tasks' })).toBeInTheDocument()
    expect(screen.getByRole('link', { name: 'Project Planner' })).toBeInTheDocument()
  })

  it('renders the dark-mode switch', () => {
    render(<Sidebar />)

    expect(screen.getByRole('checkbox')).toBeInTheDocument()
  })

  describe('user information', () => {
    it('renders user name and initials when session exists', () => {
      ;(useSession as jest.Mock).mockReturnValue({
        data: {
          user: {
            firstName: 'Jane',
            lastName: 'Smith'
          }
        }
      })

      render(<Sidebar />)

      expect(screen.getByText('Jane Smith')).toBeInTheDocument()
      expect(screen.getByText('JS')).toBeInTheDocument()
    })

    it('does not render user info when there is no session', () => {
      render(<Sidebar />)

      expect(screen.queryByText('JS')).not.toBeInTheDocument()
    })

    it('renders only first name initial when lastName is missing', () => {
      ;(useSession as jest.Mock).mockReturnValue({
        data: {
          user: {
            firstName: 'Jane',
            lastName: ''
          }
        }
      })

      render(<Sidebar />)

      expect(screen.getByText('Jane')).toBeInTheDocument()
      expect(screen.getByText('J')).toBeInTheDocument()
    })
  })
})
