import { useCallback } from 'react'

import { useAlert } from '@/ui/Alert/useAlert'
import { useForm } from '@/hooks/useForm/useForm'
import { Task, getOverlappingTasks } from '@/domain/Task'
import { useEditTask } from './useEditTask'
import { Project } from '@/domain/Project'

type UseEditTaskFormProps = {
  task: Task
  tasks: Array<Task>
  closeForm: () => void
}

export const useEditTaskForm = ({ task, tasks, closeForm }: UseEditTaskFormProps) => {
  const { formState, handleChange, resetForm } = useForm<Task>({
    initialValues: task
  })
  const { showError } = useAlert()
  const { editTask } = useEditTask({ handleSuccess: closeForm })

  const handleSubmit = useCallback(() => {
    const validation = Task.safeParse(formState)

    if (!validation.success) {
      validation.error.issues.map(({ message }) => {
        showError(message)
      })

      return
    }

    const { message } = getOverlappingTasks(
      formState,
      tasks.filter(({ id }) => id !== formState.id)
    )

    if (message.length > 0) {
      showError(message)
      return
    }

    editTask(formState)
  }, [editTask, formState, showError, tasks])

  const handleProject = (value: string, projects: Array<Project>) => {
    const project = projects.find((project) => project.description === value)
    if (project) {
      handleChange('projectId', project.id)
      handleChange('projectName', value)
    }
  }

  return { formState, handleChange, handleSubmit, resetForm, handleProject }
}
