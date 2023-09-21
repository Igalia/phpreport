import { render, screen, fireEvent } from '@/test-utils/test-utils'
import { CollapseButton } from '../CollapseButton'

describe('CollapseButton', () => {
  it('should have an invisible screen-reader text for the collapse button', () => {
    render(<CollapseButton sx={{}} onClick={() => {}} />)

    const collapseButton = screen.getByRole('button')

    expect(collapseButton).toHaveTextContent('Expand/collapse menu')
  })

  it('receives an sx to extend component style', () => {
    render(<CollapseButton sx={{ top: '60px' }} onClick={() => {}} />)
    const collapseButton = screen.getByRole('button')
    expect(collapseButton).toHaveStyle({ top: '60px' })
  })

  it('calls onClick prop when clicked', () => {
    const onClick = jest.fn()
    render(<CollapseButton sx={{}} onClick={onClick} />)
    const collapseButton = screen.getByRole('button')
    fireEvent.click(collapseButton)
    expect(onClick).toHaveBeenCalled()
  })
})
