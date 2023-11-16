import { ConfirmationModal } from '../ConfirmationModal'
import { screen, renderWithUser } from '@/test-utils/test-utils'

const setupConfirmationModal = ({
  open = true,
  closeModal = () => {},
  confirmAction = () => {}
}) => {
  return renderWithUser(
    <ConfirmationModal
      title="Confirmation Modal"
      content="This is the confirmation modal"
      confirmText="Confirm Action"
      open={open}
      closeModal={closeModal}
      confirmAction={confirmAction}
    />
  )
}

describe('ConfirmationModal', () => {
  it("doesn't show if open is false", () => {
    setupConfirmationModal({ open: false })

    expect(screen.queryByRole('heading', { name: 'Confirmation Modal' })).not.toBeInTheDocument()
  })

  it('shows if open is true', () => {
    setupConfirmationModal({})

    expect(screen.getByRole('heading', { name: 'Confirmation Modal' })).toBeInTheDocument()
    expect(screen.getByText('This is the confirmation modal')).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Cancel' })).toBeInTheDocument()
    expect(screen.getByRole('button', { name: 'Confirm Action' })).toBeInTheDocument()
  })

  it('calls closeModal when cancel is clicked', async () => {
    const closeModal = jest.fn()
    const { user } = setupConfirmationModal({ closeModal })

    await user.click(screen.getByRole('button', { name: 'Cancel' }))

    expect(closeModal).toBeCalled()
  })

  it('calls confirmAction when the confirm button is clicked', async () => {
    const confirmAction = jest.fn()
    const { user } = setupConfirmationModal({ confirmAction })

    await user.click(screen.getByRole('button', { name: 'Confirm Action' }))

    expect(confirmAction).toBeCalled()
  })
})
