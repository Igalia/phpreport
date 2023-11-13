import { TaskList } from '../TaskList'
import { screen, renderWithUser, within } from '@/test-utils/test-utils'
import { useGetTasks } from '../hooks/useTask'
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
})
