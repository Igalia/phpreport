import { useCallback } from 'react'

import { useAlert } from '@/ui/Alert/useAlert'
import { useForm } from '@/hooks/useForm/useForm'
import { Task, TaskIntent, getOverlappingTasks } from '@/domain/Task'
import { useEditTask } from './useEditTask'

type UseEditTaskFormProps = {
  task: Task
  tasks: Array<Task>
  closeForm: () => void
}

export const useEditTaskForm = ({ task, tasks, closeForm }: UseEditTaskFormProps) => {
  const { formState, handleChange, resetForm, formRef } = useForm<TaskIntent>({
    initialValues: task
  })

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

  return { formState, handleChange, handleSubmit, resetForm, formRef }
}
