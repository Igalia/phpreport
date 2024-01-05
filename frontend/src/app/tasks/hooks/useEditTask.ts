import { useMutation, useQueryClient } from '@tanstack/react-query'
import { TaskIntent, Task } from '@/domain/Task'
import { useAlert } from '@/ui/Alert/useAlert'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'
import { makeEditTask } from '@/infra/task/editTask'
import { useGetTasks } from './useGetTasks'
import { BaseError } from '@/_lib/errors/BaseError'

type UseEditTaskProps = { handleSuccess: () => void }

export const useEditTask = ({ handleSuccess }: UseEditTaskProps) => {
  const apiClient = useClientFetch()
  const editTask = makeEditTask(apiClient)
  const { tasks } = useGetTasks()
  const { showError, showSuccess } = useAlert()
  const { id: userId } = useGetCurrentUser()
  const queryClient = useQueryClient()

  const { mutate } = useMutation((task: TaskIntent) => editTask(task, tasks), {
    onSuccess: (data) => {
      queryClient.setQueryData<Array<Task>>(['tasks', userId], (prevData) =>
        prevData!.map((task) => {
          if (task.id === data.id) {
            return data
          }

          return task
        })
      )
      showSuccess('Task succesfully edited')
      handleSuccess()
    },
    onError: (e) => {
      if (e instanceof BaseError) {
        showError(e.message)
        return
      }

      throw e
    }
  })

  return { editTask: mutate }
}
