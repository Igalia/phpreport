import { MaskedSelect } from '../MaskedSelect'
import { screen, renderWithUser, act } from '@/test-utils/test-utils'
import { Options } from '../types'

const strOptions: Options = ['beach', 'mountain', 'city']
const structOptions: Options = [
  { label: 'beach', value: '0' },
  { label: 'mountain', value: '1' },
  { label: 'city', value: '2' }
]

const setupBaseSelect = ({
  value = '',
  options = [],
  onChange,
  name = 'select-name',
  label = 'Select Option'
}: {
  value?: string
  options?: Options
  onChange?: () => void
  name?: string
  label?: string
}) => {
  return renderWithUser(
    <MaskedSelect
      mask={/./}
      value={value}
      label={label}
      name={name}
      onChange={onChange}
      options={options}
    />
  )
}

describe('Select', () => {
  describe('when the options are an array of strings', () => {
    it('renders the options', async () => {
      const { user } = setupBaseSelect({ options: strOptions })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      await user.click(selectInput)

      expect(screen.getByRole('option', { name: 'beach' })).toBeVisible()
      expect(screen.getByRole('option', { name: 'mountain' })).toBeVisible()
      expect(screen.getByRole('option', { name: 'city' })).toBeVisible()
    })

    it('selects an options when it the option is clicked', async () => {
      const { user } = setupBaseSelect({ options: strOptions })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      await user.click(selectInput)

      await user.click(screen.getByRole('option', { name: 'beach' }))

      expect(selectInput).toHaveValue('beach')
    })

    it('calls the onChange function when the select is clicked if it is provided', async () => {
      const onChange = jest.fn()
      const { user } = setupBaseSelect({ options: strOptions, onChange })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      await user.click(selectInput)

      await user.click(screen.getByRole('option', { name: 'beach' }))

      expect(onChange).toHaveBeenCalledWith('beach')
    })
  })

  describe('when the options are an array of an option hash', () => {
    it('renders the options', async () => {
      const { user } = setupBaseSelect({ options: structOptions })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      await user.click(selectInput)

      expect(screen.getByRole('option', { name: 'beach' })).toBeVisible()
      expect(screen.getByRole('option', { name: 'mountain' })).toBeVisible()
      expect(screen.getByRole('option', { name: 'city' })).toBeVisible()
    })

    it('selects an options when it the option is clicked', async () => {
      const { user } = setupBaseSelect({ options: structOptions })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      await user.click(selectInput)

      await user.click(screen.getByRole('option', { name: 'beach' }))

      expect(selectInput).toHaveValue('beach')
    })

    it('calls the onChange function with the option value', async () => {
      const onChange = jest.fn()
      const { user } = setupBaseSelect({ options: structOptions, onChange })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      await user.click(selectInput)

      await user.click(screen.getByRole('option', { name: 'beach' }))

      expect(onChange).toHaveBeenCalledWith('0')
    })

    it('shows the correct display value when the option is selected', async () => {
      setupBaseSelect({ options: structOptions, value: '0' })

      const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

      expect(selectInput).toHaveValue('beach')
    })
  })

  it('opens the dropdown when the user clicks on the input', async () => {
    const { user } = setupBaseSelect({ options: strOptions })

    const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

    await user.click(selectInput)

    expect(screen.getByTestId('select-name-dropdown')).toBeVisible()
  })

  it('opens the dropdown when the user types on the input', async () => {
    const { user } = setupBaseSelect({ options: strOptions })

    const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

    act(() => {
      selectInput.focus()
    })

    await user.keyboard('m')

    expect(screen.getByTestId('select-name-dropdown')).toBeVisible()
  })

  it('filter the options when the input has a value', async () => {
    const { user } = setupBaseSelect({ options: strOptions, value: 'm' })

    const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

    act(() => {
      selectInput.focus()
    })

    await user.keyboard('{ArrowDown}')

    expect(screen.getByRole('option', { name: 'mountain' })).toBeInTheDocument()
    expect(screen.queryByRole('option', { name: 'city' })).not.toBeInTheDocument()
    expect(screen.queryByRole('option', { name: 'beach' })).not.toBeInTheDocument()
  })

  it('closes the dropdown and clears the input when the Close button is clicked', async () => {
    const onChange = jest.fn()
    const { user } = setupBaseSelect({ options: strOptions, value: 'test', onChange })

    const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

    act(() => {
      selectInput.focus()
    })

    await user.click(screen.getByRole('button', { name: 'clear input' }))

    expect(screen.getByTestId('select-name-dropdown')).not.toBeVisible()
    expect(onChange).toBeCalledWith('')
  })

  describe('Keyboard navigation', () => {
    describe('ArrowDown', () => {
      it('opens the dropdown when ArrowDown is pressed and navigates to the first option', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{ArrowDown}')

        expect(screen.getByTestId('select-name-dropdown')).toBeVisible()

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )
      })

      it('navigates to the previous option', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'mountain' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'city' })).toHaveAttribute(
          'aria-selected',
          'true'
        )
      })

      it('opens the form without selecting an option when Alt + ArrowDown is pressed', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{Alt>}{ArrowDown}')

        expect(screen.getByTestId('select-name-dropdown')).toBeVisible()
        expect(screen.getByRole('option', { name: 'city' })).not.toHaveAttribute(
          'aria-selected',
          'true'
        )
      })
    })

    describe('ArrowUp', () => {
      it('opens the dropdown when ArrowUp is pressed and navigates to the last option', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{ArrowUp}')

        expect(screen.getByTestId('select-name-dropdown')).toBeVisible()
        expect(screen.getByRole('option', { name: 'city' })).toHaveAttribute(
          'aria-selected',
          'true'
        )
      })

      it('navigates to the previous option', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{ArrowUp}')

        await user.keyboard('{ArrowUp}')

        expect(screen.getByRole('option', { name: 'mountain' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{ArrowUp}')

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{ArrowUp}')

        expect(screen.getByRole('option', { name: 'city' })).toHaveAttribute(
          'aria-selected',
          'true'
        )
      })
    })

    describe('Tab', () => {
      it('should close the dropdown and unfocus the component', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{ArrowDown}')

        await user.keyboard('{Tab}')

        expect(selectInput).not.toHaveFocus()
        expect(screen.getByTestId('select-name-dropdown')).not.toBeVisible()
      })

      it('should call onChange with the first available option if the select has a value', async () => {
        const onChange = jest.fn()
        const { user } = setupBaseSelect({ options: strOptions, onChange, value: 'b' })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{Tab}')

        expect(onChange).toHaveBeenCalledWith('beach')
      })

      it('should call onChange the select doesnt have a value', async () => {
        const onChange = jest.fn()
        const { user } = setupBaseSelect({ options: strOptions, onChange })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        act(() => {
          selectInput.focus()
        })

        await user.keyboard('{Tab}')

        expect(onChange).not.toHaveBeenCalled()
      })
    })

    describe('Enter', () => {
      it('should close the dropdown', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{Enter}')

        expect(selectInput).toHaveFocus()
        expect(screen.getByTestId('select-name-dropdown')).not.toBeVisible()
      })

      it('calls onChange with the selected option', async () => {
        const onChange = jest.fn()
        const { user } = setupBaseSelect({ options: strOptions, onChange })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{ArrowDown}')

        await user.keyboard('{Enter}')

        expect(onChange).toBeCalledWith('beach')
      })
    })

    describe('Escape', () => {
      it('closes the dropdown if displayed', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{Escape}')

        expect(screen.getByTestId('select-name-dropdown')).not.toBeVisible()
      })

      it('clears the input if the dropdown is not displayed', async () => {
        const onChange = jest.fn()
        const { user } = setupBaseSelect({ options: strOptions, onChange, value: 'm' })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{Escape}')

        await user.keyboard('{Escape}')

        expect(onChange).toHaveBeenCalledWith('')
      })
    })

    describe('ArrowLeft', () => {
      it('removes the visual indicator when ArrowLeft is pressed', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{ArrowLeft}')

        expect(screen.getByRole('option', { name: 'beach' })).not.toHaveAttribute(
          'aria-selected',
          'true'
        )
      })
    })

    describe('ArrowRight', () => {
      it('removes the visual indicator when ArrowRight is pressed', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{ArrowRight}')

        expect(screen.getByRole('option', { name: 'beach' })).not.toHaveAttribute(
          'aria-selected',
          'true'
        )
      })
    })

    describe('Home', () => {
      it('removes the visual indicator when Home is pressed', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{Home}')

        expect(screen.getByRole('option', { name: 'beach' })).not.toHaveAttribute(
          'aria-selected',
          'true'
        )
      })
    })

    describe('End', () => {
      it('removes the visual indicator when End is pressed', async () => {
        const { user } = setupBaseSelect({ options: strOptions })

        const selectInput = screen.getByRole('combobox', { name: 'Select Option' })

        await user.click(selectInput)

        await user.keyboard('{ArrowDown}')

        expect(screen.getByRole('option', { name: 'beach' })).toHaveAttribute(
          'aria-selected',
          'true'
        )

        await user.keyboard('{End}')

        expect(screen.getByRole('option', { name: 'beach' })).not.toHaveAttribute(
          'aria-selected',
          'true'
        )
      })
    })
  })
})
