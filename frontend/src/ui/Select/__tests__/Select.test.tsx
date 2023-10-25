import { Select } from '../Select'
import { screen, renderWithUser, act } from '@/test-utils/test-utils'
import { Options } from '../types'

const strOptions = ['beach', 'mountain', 'city']
const structOptions = [
  { label: 'beach', value: 0 },
  { label: 'mountain', value: 1 },
  { label: 'city', value: 2 }
]

const setupBaseSelect = ({
  value = '',
  options = [] as Options,
  onChange = () => {},
  name = 'select-name',
  label = 'Select Option'
}) => {
  return renderWithUser(
    <Select value={value} label={label} name={name} onChange={onChange} options={options} />
  )
}

describe('BaseSelect', () => {
  it('renders the component with string options and selects an option', async () => {
    const onChange = jest.fn()
    const { user } = setupBaseSelect({ options: strOptions, onChange })

    const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

    await user.click(selectInput)

    expect(screen.getByRole('listbox')).toBeVisible()

    await user.click(screen.getByRole('option', { name: 'beach' }))

    expect(onChange).toBeCalledWith('beach')
  })
})
