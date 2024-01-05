import { useForm } from '@/hooks/useForm/useForm'
import { Task, TaskIntent } from '@/domain/Task'
import { useEditTask } from './useEditTask'

type UseEditTaskFormProps = {
  task: Task
  closeForm: () => void
}

export const useEditTaskForm = ({ task, closeForm }: UseEditTaskFormProps) => {
  const { formState, handleChange, resetForm, formRef } = useForm<TaskIntent>({
    initialValues: task
  })
  const { editTask } = useEditTask({ handleSuccess: closeForm })

  const handleSubmit = () => {
    editTask(formState)
  }

  return { formState, handleChange, handleSubmit, resetForm, formRef }
}
