import { renderWithUser, screen } from '@/test-utils/test-utils'
import { TaskBox } from '../../components/TaskBox'
import { useDeleteTask } from '../../hooks/useDeleteTask'
import { useCreateTaskForm } from '../../day/[[...date]]/hooks/useCreateTaskForm'

jest.mock('../../hooks/useDeleteTask')
jest.mock('../../day/[[...date]]/hooks/useCreateTaskForm')

const setupTaskBox = () => {
  const task = {
    date: '2023-11-01',
    story: 'task story',
    description: 'task description',
    taskType: 'task type one',
    projectId: 1,
    userId: 4,
    startTime: '11:12',
    endTime: '11:14',
    id: 18,
    projectName: 'Holidays',
    customerName: 'Internal'
  }

  return renderWithUser(<TaskBox projects={[]} taskTypes={[]} task={task} />)
}

describe('TaskBox', () => {
  beforeEach(() => {
    ;(useDeleteTask as jest.Mock).mockReturnValue({ removeTask: () => {} })
    ;(useCreateTaskForm as jest.Mock).mockReturnValue({ cloneTask: () => {} })
  })

  it('Shows the task info', () => {
    setupTaskBox()

    expect(screen.getByText('Holidays - Internal')).toBeInTheDocument()
    expect(screen.getByText('task type one')).toBeInTheDocument()
    expect(screen.getByText('11:12-11:14 (0h 2m)')).toBeInTheDocument()
  })

  describe('Clicking expand Button', () => {
    it('shows the complete task information', async () => {
      const { user } = setupTaskBox()

      await user.click(screen.getByRole('button', { name: 'Expand Task' }))

      expect(screen.getByText('task description')).toBeInTheDocument()
      expect(screen.getByText('task story')).toBeInTheDocument()
    })
  })

  describe('Clicking delete button', () => {
    it('Opens the delete confirmation modal', async () => {
      const { user } = setupTaskBox()

      await user.click(screen.getByRole('button', { name: 'Delete task' }))

      expect(screen.getByRole('heading', { name: 'Confirm Deletion' })).toBeInTheDocument()
    })

    it('deletes the task from the list', async () => {
      const deleteTask = jest.fn()
      ;(useDeleteTask as jest.Mock).mockReturnValue({ deleteTask })

      const { user } = setupTaskBox()

      await user.click(screen.getByRole('button', { name: 'Delete task' }))

      await user.click(screen.getByRole('button', { name: 'Delete' }))

      expect(deleteTask).toBeCalledWith(18)
    })

    it('closes the modal without submit if cancel is clicked', async () => {
      const deleteTask = jest.fn()
      ;(useDeleteTask as jest.Mock).mockReturnValue({ deleteTask })

      const { user } = setupTaskBox()

      await user.click(screen.getByRole('button', { name: 'Delete task' }))

      await user.click(screen.getByRole('button', { name: 'Cancel' }))

      expect(deleteTask).not.toBeCalled()
    })
  })

  describe('Clicking clone task button', () => {
    it('calls the clone task function', async () => {
      const cloneTask = jest.fn()
      ;(useCreateTaskForm as jest.Mock).mockReturnValue({ cloneTask })

      const { user } = setupTaskBox()

      await user.click(screen.getByRole('button', { name: 'Clone Task' }))

      expect(cloneTask).toBeCalled()
    })
  })
})
