import { Sidebar } from '../Sidebar'
import { render, screen } from '@/test-utils/test-utils'

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
})
