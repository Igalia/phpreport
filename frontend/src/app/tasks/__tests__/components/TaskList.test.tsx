import { TaskList } from '../../components/TaskList'
import { screen, renderWithUser } from '@/test-utils/test-utils'
import { useGetTasks } from '../../hooks/useGetTasks'
import { useDeleteTask } from '../../hooks/useDeleteTask'
import { Task } from '@/domain/Task'

jest.mock('../../hooks/useGetTasks')
jest.mock('../../hooks/useDeleteTask')

const setupTaskList = () => {
  return renderWithUser(<TaskList projects={[]} taskTypes={[]} />)
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
    ;(useGetTasks as jest.Mock).mockReturnValue({ tasks })
    ;(useDeleteTask as jest.Mock).mockReturnValue({ removeTask: () => {} })
  })

  it('renders a list of tasks', () => {
    setupTaskList()

    const listItems = screen.getAllByRole('listitem')

    expect(listItems.length).toBe(2)
  })
})
