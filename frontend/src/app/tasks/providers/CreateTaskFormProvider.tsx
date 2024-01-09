import { format } from 'date-fns'
import { useCallback, useState, createContext, PropsWithChildren, RefObject, useMemo } from 'react'

import { useForm } from '@/hooks/useForm/useForm'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'

import { useCreateTask } from '../hooks/useCreateTask'

import { Template } from '@/domain/Template'

import { TaskIntent, Task } from '@/domain/Task'

type CreateTaskFormContext = {
  task: TaskIntent
  handleChange: ReturnType<typeof useForm<TaskIntent>>['handleChange']
  resetForm: ReturnType<typeof useForm<TaskIntent>>['resetForm']
  handleSubmit: () => void
  formRef: RefObject<HTMLFormElement>
  selectTemplate: (template: Template | null) => void
  template: Template | null
  cloneTask: (task: Task) => void
  isLoading: boolean
}

export const CreateTaskFormContext = createContext({} as CreateTaskFormContext)

export const CreateTaskFormProvider = ({ children }: PropsWithChildren) => {
  const { id: userId } = useGetCurrentUser()
  const { addTask, isLoading } = useCreateTask()
  const [template, setTemplate] = useState<Template | null>(null)

  const { formState, handleChange, resetForm, setFormState, formRef } = useForm<TaskIntent>({
    initialValues: {
      userId,
      projectId: null,
      taskType: '',
      story: '',
      description: '',
      startTime: '',
      endTime: '',
      date: format(new Date(), 'yyyy-MM-dd')
    }
  })

  const handleSubmit = useCallback(() => {
    addTask({ task: formState, handleSuccess: resetForm })
  }, [addTask, formState, resetForm])

  const selectTemplate = useCallback(
    (template: Template | null) => {
      setTemplate(template)
      if (template) {
        setFormState((prevState) => ({
          ...prevState,
          taskType: template.taskType,
          description: template.description || '',
          startTime: template.startTime || '',
          endTime: template.endTime || '',
          projectId: template.projectId,
          story: template.story || ''
        }))
      }
    },
    [setFormState]
  )

  const cloneTask = useCallback(
    ({ taskType, projectId, story, description }: Task) => {
      setFormState((prevState) => ({
        ...prevState,
        taskType,
        projectId,
        story,
        description
      }))
    },
    [setFormState]
  )

  const value = useMemo(
    () => ({
      task: formState,
      handleChange,
      resetForm,
      handleSubmit,
      formRef,
      selectTemplate,
      template,
      cloneTask,
      isLoading
    }),
    [
      cloneTask,
      formRef,
      formState,
      handleChange,
      handleSubmit,
      resetForm,
      selectTemplate,
      template,
      isLoading
    ]
  )

  return <CreateTaskFormContext.Provider value={value}>{children}</CreateTaskFormContext.Provider>
}
