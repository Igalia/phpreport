import { useCallback, useEffect, useRef } from 'react'

import { useAlert } from '@/ui/Alert/useAlert'
import { useForm } from '@/hooks/useForm/useForm'
import { Task, TaskIntent, getOverlappingTasks } from '@/domain/Task'
import { useEditTask } from './useEditTask'
import { Project } from '@/domain/Project'

type UseEditTaskFormProps = {
  task: Task
  tasks: Array<Task>
  closeForm: () => void
}

export const useEditTaskForm = ({ task, tasks, closeForm }: UseEditTaskFormProps) => {
  const { formState, handleChange, resetForm } = useForm<TaskIntent>({
    initialValues: task
  })
  const formRef = useRef<HTMLFormElement>(null)

  const { showError } = useAlert()
  const { editTask } = useEditTask({ handleSuccess: closeForm })

  const handleSubmit = useCallback(() => {
    const validation = TaskIntent.safeParse(formState)

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
    handleChange('projectName', value)
    if (project) {
      handleChange('projectId', project.id)
    } else {
      handleChange('projectId', null)
    }
  }

  useEffect(() => {
    const handleKeyPress = (event: KeyboardEvent) => {
      if (event.key === 's' && event.ctrlKey && formRef.current?.contains(document.activeElement)) {
        event.preventDefault()
        formRef.current?.requestSubmit()
      }
    }
    document.addEventListener('keydown', handleKeyPress)

    return () => {
      document.removeEventListener('keydown', handleKeyPress)
    }
  }, [handleSubmit])

  return { formState, handleChange, handleSubmit, resetForm, handleProject, formRef }
}
