import { TaskForm } from '../TaskForm'
import { screen, setup, act } from '@/test-utils/test-utils'

jest.mock('../../user/hooks/useCurrentUser', () => ({
  useCurrentUser: () => ({ user: { id: 0 } })
}))

jest.mock('../../../ui/Alert/useAlert', () => ({
  useAlert: () => ({
    showSuccess: () => {},
    showError: () => {}
  })
}))

jest.mock('../hooks/useTask', () => ({
  useAddTask: () => ({
    addTask: () => {}
  })
}))

jest.mock('../hooks/useProjects', () => ({
  useProjects: () => ({
    projects: [
      {
        id: '1',
        is_active: true,
        init: null,
        end: null,
        invoice: null,
        estimated_hours: null,
        moved_hours: null,
        description: 'Holidays',
        project_type: null,
        schedule_type: null,
        customer_id: 1,
        area_id: 1
      }
    ],
    isLoading: false
  })
}))

jest.mock('../hooks/useTaskTypes', () => ({
  useTaskTypes: () => [
    { name: 'mock task type', slug: 'mock-test', active: true },
    { name: 'mock task type 2', slug: 'mock-test-2', active: true }
  ]
}))

describe('TasksPage', () => {
  beforeEach(() => {
    jest.useFakeTimers()
    jest.spyOn(global, 'setInterval')
    jest.setSystemTime(new Date('January 01, 2023 23:15:00'))
  })

  afterEach(() => {
    jest.clearAllTimers()
  })

  describe('when the timer is activated', () => {
    it('activates by clicking Start Timer', async () => {
      const { user } = setup(<TaskForm />, { advanceTimers: jest.advanceTimersByTime })

      const timerButton = screen.getByRole('button', { name: 'Start Timer' })

      await user.click(timerButton)

      act(() => {
        jest.advanceTimersByTime(30000)
      })

      expect(screen.getByText('0h 0m 30s')).toBeInTheDocument()
    })

    it('stops by clicking Stop Timer', async () => {
      const { user } = setup(<TaskForm />, { advanceTimers: jest.advanceTimersByTime })

      const startTimerButton = screen.getByRole('button', { name: 'Start Timer' })

      await user.click(startTimerButton)

      act(() => {
        jest.advanceTimersByTime(30000)
      })

      const stopTimerButton = screen.getByRole('button', { name: 'Stop Timer' })

      await user.click(stopTimerButton)

      expect(screen.getByText('0h 0m 0s')).toBeInTheDocument()
    })

    it('disables the startTime and endTime selects when the timer is running', async () => {
      const { user } = setup(<TaskForm />, { advanceTimers: jest.advanceTimersByTime })

      const startTimerButton = screen.getByRole('button', { name: 'Start Timer' })

      await user.click(startTimerButton)

      const startTimeSelect = screen.getByRole('combobox', { name: 'From' })
      const endTimeSelect = screen.getByRole('combobox', { name: 'To' })

      expect(startTimeSelect).toBeDisabled()
      expect(endTimeSelect).toBeDisabled()

      const stopTimerButton = screen.getByRole('button', { name: 'Stop Timer' })

      await user.click(stopTimerButton)

      expect(startTimeSelect).not.toBeDisabled()
      expect(endTimeSelect).not.toBeDisabled()
    })

    it('changes the value of the startTime and endTime select', async () => {
      const { user } = setup(<TaskForm />, { advanceTimers: jest.advanceTimersByTime })

      await user.click(screen.getByRole('button', { name: 'Start Timer' }))

      const startTimeSelect = screen.getByRole('combobox', { name: 'From' })
      const endTimeSelect = screen.getByRole('combobox', { name: 'To' })

      act(() => {
        jest.advanceTimersByTime(300000)
      })

      await user.click(screen.getByRole('button', { name: 'Stop Timer' }))

      expect(startTimeSelect).toHaveValue('23:15')
      expect(endTimeSelect).toHaveValue('23:20')
    })
  })

  it('manually edits the starTime and endTime by typing', async () => {
    const { user } = setup(<TaskForm />, { advanceTimers: jest.advanceTimersByTime })

    const startTimeSelect = screen.getByRole('combobox', { name: 'From' })
    const endTimeSelect = screen.getByRole('combobox', { name: 'To' })

    await user.type(startTimeSelect, '23:15')
    await user.type(endTimeSelect, '23:20')

    expect(startTimeSelect).toHaveValue('23:15')
    expect(endTimeSelect).toHaveValue('23:20')
  })

  it('changes the field values and clear it', async () => {
    const { user } = setup(<TaskForm />, { advanceTimers: jest.advanceTimersByTime })

    const projectSelect = screen.getByRole('combobox', { name: 'Select project' })
    const startTimeSelect = screen.getByRole('combobox', { name: 'From' })
    const endTimeSelect = screen.getByRole('combobox', { name: 'To' })
    const descriptionInput = screen.getByRole('textbox', { name: 'Task description' })
    const taskTypeSelect = screen.getByRole('combobox', { name: 'Select task type' })
    const storyInput = screen.getByRole('textbox', { name: 'Story' })

    await user.click(projectSelect)
    await user.click(screen.getByRole('option', { name: 'Holidays' }))

    await user.click(startTimeSelect)
    await user.click(screen.getByRole('option', { name: '12:00' }))

    await user.click(endTimeSelect)
    await user.click(screen.getByRole('option', { name: '13:00' }))

    await user.type(descriptionInput, 'description!')

    await user.click(taskTypeSelect)
    await user.click(screen.getByRole('option', { name: 'mock task type' }))

    await user.type(storyInput, 'story!')

    expect(projectSelect).toHaveValue('Holidays')
    expect(startTimeSelect).toHaveValue('12:00')
    expect(endTimeSelect).toHaveValue('13:00')
    expect(descriptionInput).toHaveValue('description!')
    expect(taskTypeSelect).toHaveValue('mock task type')
    expect(storyInput).toHaveValue('story!')

    await user.click(screen.getByRole('button', { name: 'Clear' }))

    expect(projectSelect).toHaveValue('')
    expect(startTimeSelect).toHaveValue('')
    expect(endTimeSelect).toHaveValue('')
    expect(descriptionInput).toHaveValue('')
    expect(taskTypeSelect).toHaveValue('')
    expect(storyInput).toHaveValue('')
  })
})
