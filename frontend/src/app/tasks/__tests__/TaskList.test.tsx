import { TaskList } from '../TaskList'
import { screen, renderWithUser, within } from '@/test-utils/test-utils'
import { useGetTasks, useDeleteTask } from '../hooks/useTask'
import { Task } from '@/domain/Task'

jest.mock('../hooks/useTask')

const setupTaskList = () => {
  return renderWithUser(<TaskList userId={0} />)
}

describe('TaskList', () => {
  let tasks: Array<Task>

  beforeEach(() => {
    tasks = [
      {
        date: '2023-11-01',
        story: '',
        description: 'test',
        taskType: 'task type one',
        projectId: 1,
        userId: 4,
        startTime: '11:12',
        endTime: '11:14',
        id: 18,
        projectName: 'Holidays',
        customerName: 'Internal'
      },
      {
        date: '2023-11-01',
        story: '',
        description: 'test',
        taskType: 'task type two',
        projectId: 1,
        userId: 4,
        startTime: '12:17',
        endTime: '14:19',
        id: 19,
        projectName: 'Vacations',
        customerName: 'Igalia'
      }
    ]
    ;(useGetTasks as jest.Mock).mockReturnValue(tasks)
    ;(useDeleteTask as jest.Mock).mockReturnValue({ removeTask: () => {} })
  })

  it('renders a list of tasks with name, customer name, task type and the task duration', () => {
    setupTaskList()

    const listItems = screen.getAllByRole('listitem')

    expect(listItems.length).toBe(2)

    listItems.forEach((item, index) => {
      const { getByText } = within(item)
      const task = tasks[index]

      const timeDifference = index === 0 ? '0h 2m' : '2h 2m'

      expect(getByText(`${task.projectName} - ${task.customerName}`)).toBeInTheDocument()
      expect(getByText(`${task.taskType}`)).toBeInTheDocument()
      expect(
        getByText(`${task.startTime}-${task.endTime} (${timeDifference})`, {
          exact: false
        })
      ).toBeInTheDocument()
    })
  })

  describe('When the trash icon is clicked', () => {
    it('opens the delete confirmation modal', async () => {
      const { user } = setupTaskList()

      await user.click(screen.getByRole('button', { name: 'Delete task 18' }))

      expect(screen.getByRole('heading', { name: 'Confirm Deletion' })).toBeInTheDocument()
    })

    it('deletes the task from the list', async () => {
      const deleteTask = jest.fn()
      ;(useDeleteTask as jest.Mock).mockReturnValue({ deleteTask })

      const { user } = setupTaskList()

      await user.click(screen.getByRole('button', { name: 'Delete task 18' }))

      await user.click(screen.getByRole('button', { name: 'Delete' }))

      expect(deleteTask).toBeCalledWith(18)
    })

    it('closes the modal without submit if cancel is clicked', async () => {
      const deleteTask = jest.fn()
      ;(useDeleteTask as jest.Mock).mockReturnValue({ deleteTask })

      const { user } = setupTaskList()

      await user.click(screen.getByRole('button', { name: 'Delete task 18' }))

      await user.click(screen.getByRole('button', { name: 'Cancel' }))

      expect(deleteTask).not.toBeCalled()
    })
  })
})
