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

  describe('support links', () => {
    afterEach(() => {
      delete process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_URL
      delete process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_TEXT
    })

    it('renders support links when env vars are set', () => {
      process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_URL = 'https://example.com/issues'
      process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_TEXT = 'Report an issue'

      render(<Sidebar />)

      const link = screen.getByRole('link', { name: 'Report an issue' })
      expect(link).toBeInTheDocument()
      expect(link).toHaveAttribute('href', 'https://example.com/issues')
      expect(link).toHaveAttribute('target', '_blank')
      expect(link).toHaveAttribute('rel', 'noopener noreferrer')
    })

    it('renders multiple support links', () => {
      process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_URL = 'https://example.com/issues,https://example.com/docs'
      process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_TEXT = 'Report an issue,Documentation'

      render(<Sidebar />)

      expect(screen.getByRole('link', { name: 'Report an issue' })).toBeInTheDocument()
      expect(screen.getByRole('link', { name: 'Documentation' })).toBeInTheDocument()
    })

    it('does not render support links when env vars are not set', () => {
      render(<Sidebar />)

      expect(screen.queryByRole('link', { name: 'Report an issue' })).not.toBeInTheDocument()
    })

    it('uses URL as text when text is missing', () => {
      process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_URL = 'https://example.com/issues'
      process.env.NEXT_PUBLIC_ISSUE_TRACKER_LINKS_TEXT = ''

      render(<Sidebar />)

      const link = screen.getByRole('link', { name: 'https://example.com/issues' })
      expect(link).toBeInTheDocument()
    })
  })
})
