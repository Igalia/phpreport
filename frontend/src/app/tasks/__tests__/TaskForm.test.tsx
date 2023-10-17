import { TaskForm } from '../TaskForm'
import { screen, renderWithUser, act } from '@/test-utils/test-utils'

const setupTaskForm = () => {
  const projects = [
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
  ]

  const taskTypes = [
    { name: 'mock task type', slug: 'mock-test', active: true },
    { name: 'mock task type 2', slug: 'mock-test-2', active: true }
  ]

  return renderWithUser(<TaskForm projects={projects} taskTypes={taskTypes} />, {
    advanceTimers: jest.advanceTimersByTime
  })
}

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
      const { user } = setupTaskForm()

      const timerButton = screen.getByRole('button', { name: 'Start Timer' })

      await user.click(timerButton)

      act(() => {
        jest.advanceTimersByTime(30000)
      })

      expect(screen.getByText('0h 0m 30s')).toBeInTheDocument()
    })

    it('stops by clicking Stop Timer', async () => {
      const { user } = setupTaskForm()

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
      const { user } = setupTaskForm()

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
      const { user } = setupTaskForm()

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
    const { user } = setupTaskForm()

    const startTimeSelect = screen.getByRole('combobox', { name: 'From' })
    const endTimeSelect = screen.getByRole('combobox', { name: 'To' })

    await user.type(startTimeSelect, '23:15')
    await user.type(endTimeSelect, '23:20')

    expect(startTimeSelect).toHaveValue('23:15')
    expect(endTimeSelect).toHaveValue('23:20')
  })

  it('changes the field values and clear it', async () => {
    const { user } = setupTaskForm()

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

  it('submits the form when clicking Save', async () => {
    const addTask = jest.fn()

    const { user } = setupTaskForm()

    await user.click(screen.getByRole('combobox', { name: 'Select project' }))
    await user.click(screen.getByRole('option', { name: 'Holidays' }))

    await user.click(screen.getByRole('combobox', { name: 'From' }))
    await user.click(screen.getByRole('option', { name: '12:00' }))

    await user.click(screen.getByRole('combobox', { name: 'To' }))
    await user.click(screen.getByRole('option', { name: '13:00' }))

    await user.type(screen.getByRole('textbox', { name: 'Task description' }), 'description!')

    await user.click(screen.getByRole('combobox', { name: 'Select task type' }))
    await user.click(screen.getByRole('option', { name: 'mock task type' }))

    await user.type(screen.getByRole('textbox', { name: 'Story' }), 'story!')

    await user.click(screen.getByRole('button', { name: 'Save' }))

    expect(addTask).toHaveBeenCalledWith({
      date: '2023-01-01',
      description: 'description!',
      endTime: '13:00',
      projectId: '1',
      startTime: '12:00',
      story: 'story!',
      taskType: 'mock-test',
      userId: 0
    })
  })

  it('submits the form when pressing ctrl+S', async () => {
    const addTask = jest.fn()

    const { user } = setupTaskForm()

    await user.click(screen.getByRole('combobox', { name: 'Select project' }))
    await user.click(screen.getByRole('option', { name: 'Holidays' }))

    await user.click(screen.getByRole('combobox', { name: 'From' }))
    await user.click(screen.getByRole('option', { name: '12:00' }))

    await user.click(screen.getByRole('combobox', { name: 'To' }))
    await user.click(screen.getByRole('option', { name: '13:00' }))

    await user.type(screen.getByRole('textbox', { name: 'Task description' }), 'description!')

    await user.click(screen.getByRole('combobox', { name: 'Select task type' }))
    await user.click(screen.getByRole('option', { name: 'mock task type' }))

    await user.type(screen.getByRole('textbox', { name: 'Story' }), 'story!')

    await user.keyboard('{Control>}s')

    expect(addTask).toHaveBeenCalledWith({
      date: '2023-01-01',
      description: 'description!',
      endTime: '13:00',
      projectId: '1',
      startTime: '12:00',
      story: 'story!',
      taskType: 'mock-test',
      userId: 0
    })
  })
})
