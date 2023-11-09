import { Task, TaskIntent, getOverlappingTasks } from '../Task'

describe('Task', () => {
  describe('getOverlappingTasks', () => {
    it("if the date is not the same it doesn't overlap", () => {
      const tasks: Array<Task> = [
        {
          date: '2023-11-01',
          story: '',
          description: 'test',
          taskType: null,
          projectId: 1,
          userId: 4,
          startTime: '11:12',
          endTime: '11:14',
          id: 18,
          init: 672,
          end: 674,
          projectName: 'Holidays',
          customerName: 'Internal'
        },
        {
          date: '2023-11-01',
          story: '',
          description: 'test',
          taskType: null,
          projectId: 1,
          userId: 4,
          startTime: '11:17',
          endTime: '11:19',
          id: 19,
          init: 677,
          end: 679,
          projectName: 'Holidays',
          customerName: 'Internal'
        }
      ]

      const newTask: TaskIntent = {
        date: '2023-12-01',
        description: 'description!',
        startTime: '11:13',
        endTime: '11:15',
        projectId: '1',
        story: 'story!',
        taskType: 'mock-test',
        userId: 0
      }

      const overlappingTasks = getOverlappingTasks(newTask, tasks)

      expect(overlappingTasks).toEqual({
        message: '',
        overlappingTasks: []
      })
    })

    describe('when the date is the same', () => {
      it('overlaps if the new task is endTime is after a task startTime and new task is startTime is before a task startTime', () => {
        const tasks: Array<Task> = [
          {
            date: '2023-11-01',
            story: '',
            description: 'test',
            taskType: null,
            projectId: 1,
            userId: 4,
            startTime: '11:12',
            endTime: '11:14',
            id: 18,
            init: 672,
            end: 674,
            projectName: 'Holidays',
            customerName: 'Internal'
          },
          {
            date: '2023-11-01',
            story: '',
            description: 'test',
            taskType: null,
            projectId: 1,
            userId: 4,
            startTime: '11:17',
            endTime: '11:19',
            id: 19,
            init: 677,
            end: 679,
            projectName: 'Holidays',
            customerName: 'Internal'
          }
        ]

        const newTask: TaskIntent = {
          date: '2023-11-01',
          description: 'description!',
          startTime: '11:13',
          endTime: '11:15',
          projectId: '1',
          story: 'story!',
          taskType: 'mock-test',
          userId: 0
        }

        const overlappingTasks = getOverlappingTasks(newTask, tasks)

        expect(overlappingTasks).toEqual({
          message: 'Task from 11:13 to 11:15 overlaps with task from 11:12 to 11:14. ',
          overlappingTasks: [
            {
              date: '2023-11-01',
              story: '',
              description: 'test',
              taskType: null,
              projectId: 1,
              userId: 4,
              startTime: '11:12',
              endTime: '11:14',
              id: 18,
              init: 672,
              end: 674,
              projectName: 'Holidays',
              customerName: 'Internal'
            }
          ]
        })
      })

      it('overlaps if the new task startTime is the same as an existing task startTime', () => {
        const tasks: Array<Task> = [
          {
            date: '2023-11-01',
            story: '',
            description: 'test',
            taskType: null,
            projectId: 1,
            userId: 4,
            startTime: '11:12',
            endTime: '11:14',
            id: 18,
            init: 672,
            end: 674,
            projectName: 'Holidays',
            customerName: 'Internal'
          },
          {
            date: '2023-11-01',
            story: '',
            description: 'test',
            taskType: null,
            projectId: 1,
            userId: 4,
            startTime: '11:17',
            endTime: '11:19',
            id: 19,
            init: 677,
            end: 679,
            projectName: 'Holidays',
            customerName: 'Internal'
          }
        ]

        const newTask: TaskIntent = {
          date: '2023-11-01',
          description: 'description!',
          startTime: '11:12',
          endTime: '11:13',
          projectId: '1',
          story: 'story!',
          taskType: 'mock-test',
          userId: 0
        }

        const overlappingTasks = getOverlappingTasks(newTask, tasks)

        expect(overlappingTasks).toEqual({
          message: 'Task from 11:12 to 11:13 overlaps with task from 11:12 to 11:14. ',
          overlappingTasks: [
            {
              date: '2023-11-01',
              story: '',
              description: 'test',
              taskType: null,
              projectId: 1,
              userId: 4,
              startTime: '11:12',
              endTime: '11:14',
              id: 18,
              init: 672,
              end: 674,
              projectName: 'Holidays',
              customerName: 'Internal'
            }
          ]
        })
      })

      it('overlaps if the new task endTime is the same as an existing task endTime', () => {
        const tasks: Array<Task> = [
          {
            date: '2023-11-01',
            story: '',
            description: 'test',
            taskType: null,
            projectId: 1,
            userId: 4,
            startTime: '11:12',
            endTime: '11:14',
            id: 18,
            init: 672,
            end: 674,
            projectName: 'Holidays',
            customerName: 'Internal'
          },
          {
            date: '2023-11-01',
            story: '',
            description: 'test',
            taskType: null,
            projectId: 1,
            userId: 4,
            startTime: '11:17',
            endTime: '11:19',
            id: 19,
            init: 677,
            end: 679,
            projectName: 'Holidays',
            customerName: 'Internal'
          }
        ]

        const newTask: TaskIntent = {
          date: '2023-11-01',
          description: 'description!',
          startTime: '11:18',
          endTime: '11:19',
          projectId: '1',
          story: 'story!',
          taskType: 'mock-test',
          userId: 0
        }

        const overlappingTasks = getOverlappingTasks(newTask, tasks)

        expect(overlappingTasks).toEqual({
          message: 'Task from 11:18 to 11:19 overlaps with task from 11:17 to 11:19. ',
          overlappingTasks: [
            {
              date: '2023-11-01',
              story: '',
              description: 'test',
              taskType: null,
              projectId: 1,
              userId: 4,
              startTime: '11:17',
              endTime: '11:19',
              id: 19,
              init: 677,
              end: 679,
              projectName: 'Holidays',
              customerName: 'Internal'
            }
          ]
        })
      })
    })
  })
})
