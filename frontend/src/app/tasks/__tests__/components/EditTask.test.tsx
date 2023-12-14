import { EditTask } from '../../components/EditTask'
import { screen, renderWithUser } from '@/test-utils/test-utils'
import { useEditTask } from '../../hooks/useTask'

jest.mock('../../hooks/useTask')

const setupEditTaskForm = ({ closeForm = () => {} }: { closeForm?: () => void }) => {
  const tasks = [
    {
      date: '2023-12-12',
      story: 'story',
      description: 'task description',
      taskType: 'task-type-0',
      projectId: 0,
      userId: 0,
      startTime: '13:50',
      endTime: '14:00',
      id: 0,
      projectName: 'Holidays',
      customerName: 'customer'
    }
  ]

  const task = {
    date: '2023-12-12',
    story: 'story',
    description: 'task description',
    taskType: 'task-type-0',
    projectId: 0,
    userId: 0,
    startTime: '13:50',
    endTime: '14:00',
    id: 0,
    projectName: 'Holidays',
    customerName: 'customer'
  }

  const projects = [
    {
      id: 1,
      isActive: true,
      invoice: null,
      init: '2023-11-17',
      end: '2023-11-17',
      estimatedHours: null,
      movedHours: null,
      description: 'Holidays',
      projectType: null,
      scheduleType: null,
      customerId: 1,
      areaId: 1
    }
  ]

  const taskTypes = [
    { name: 'mock task type', slug: 'task-type-0', active: true },
    { name: 'mock task type 2', slug: 'mock-test-2', active: true }
  ]

  return renderWithUser(
    <EditTask
      closeForm={closeForm}
      projects={projects}
      taskTypes={taskTypes}
      tasks={tasks}
      task={task}
    />
  )
}

describe('EditTask', () => {
  beforeEach(() => {
    ;(useEditTask as jest.Mock).mockReturnValue({ editTask: () => {} })
  })

  it('Calls closeForm after clicking on Close form button', async () => {
    const closeForm = jest.fn()
    const { user } = setupEditTaskForm({ closeForm })

    await user.click(screen.getByRole('button', { name: 'Close Form' }))

    expect(closeForm).toBeCalled()
  })

  describe('Validation', () => {
    it("Doesn't submit if it doesn't have a selected project", async () => {
      const editTask = jest.fn()
      ;(useEditTask as jest.Mock).mockReturnValue({ editTask })

      const { user } = setupEditTaskForm({})

      await user.clear(screen.getByRole('combobox', { name: 'Select project' }))

      await user.type(
        screen.getByRole('combobox', { name: 'Select project' }),
        'non existent project'
      )

      await user.keyboard('{Tab}')

      await user.keyboard('{Enter}')

      expect(editTask).not.toHaveBeenCalled()
    })

    it("Doesn't submit if it doesn't have a start time", async () => {
      const editTask = jest.fn()
      ;(useEditTask as jest.Mock).mockReturnValue({ editTask })

      const { user } = setupEditTaskForm({})

      await user.clear(screen.getByRole('combobox', { name: 'From' }))

      await user.keyboard('{Enter}')

      expect(editTask).not.toHaveBeenCalled()
    })

    it("Doesn't submit if it doesn't have a end time", async () => {
      const editTask = jest.fn()
      ;(useEditTask as jest.Mock).mockReturnValue({ editTask })

      const { user } = setupEditTaskForm({})

      await user.clear(screen.getByRole('combobox', { name: 'To' }))

      await user.keyboard('{Enter}')

      expect(editTask).not.toHaveBeenCalled()
    })
  })

  describe('Submit', () => {
    it('Submits the form with the correct values', async () => {
      const editTask = jest.fn()
      ;(useEditTask as jest.Mock).mockReturnValue({ editTask })

      const { user } = setupEditTaskForm({})

      await user.clear(screen.getByRole('combobox', { name: 'Select project' }))
      await user.type(screen.getByRole('combobox', { name: 'Select project' }), 'Holi')
      await user.click(screen.getByRole('option', { name: 'Holidays' }))

      await user.clear(screen.getByRole('combobox', { name: 'To' }))
      await user.type(screen.getByRole('combobox', { name: 'To' }), '0:15')

      await user.clear(screen.getByRole('combobox', { name: 'From' }))
      await user.type(screen.getByRole('combobox', { name: 'From' }), '0:00')

      await user.type(screen.getByRole('textbox', { name: 'Task description' }), '!')

      await user.clear(screen.getByRole('combobox', { name: 'Select task type' }))
      await user.click(screen.getByRole('combobox', { name: 'Select task type' }))
      await user.click(screen.getByRole('option', { name: 'mock-test-2' }))

      await user.type(screen.getByRole('textbox', { name: 'Story' }), '!')

      await user.click(screen.getByRole('button', { name: 'Save' }))

      expect(editTask).toHaveBeenCalledWith({
        date: '2023-12-12',
        customerName: 'customer',
        description: 'task description!',
        endTime: '0:15',
        startTime: '0:00',
        story: 'story!',
        taskType: 'mock-test-2',
        userId: 0,
        id: 0,
        projectId: 1,
        projectName: 'Holidays'
      })
    })
  })
})
