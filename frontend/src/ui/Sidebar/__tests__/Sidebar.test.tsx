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
    expect(screen.getByRole('link', { name: 'Vacation Management' })).toBeInTheDocument()
    expect(screen.getByRole('link', { name: 'Reports' })).toBeInTheDocument()
    expect(screen.getByRole('link', { name: 'Data Management' })).toBeInTheDocument()
  })

  it('renders the dark-mode switch', () => {
    render(<Sidebar />)

    expect(screen.getByRole('checkbox')).toBeInTheDocument()
  })
})
