import { render, screen } from '@/test-utils/test-utils'
import { ScreenReaderOnly } from '../ScreenReaderOnly'

describe('ScreenReaderOnly', () => {
  it('renders the component with children', () => {
    render(<ScreenReaderOnly>Test text</ScreenReaderOnly>)

    expect(screen.getByText('Test text')).toBeInTheDocument()
  })
})
