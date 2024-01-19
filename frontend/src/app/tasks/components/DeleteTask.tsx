import { ConfirmationModal } from '@/ui/ConfirmationModal/ConfirmationModal'
import { useState } from 'react'
import { useDeleteTask } from '../hooks/useDeleteTask'
import { Task } from '@/domain/Task'
import { IconButton } from './IconButton'
import { Delete24Filled } from '@fluentui/react-icons'

type DeleTaskProps = {
  taskId: Task['id']
}

export const DeleteTask = ({ taskId }: DeleTaskProps) => {
  const { deleteTask } = useDeleteTask()
  const [deleteModalOpen, setDeleteModalOpen] = useState(false)

  return (
    <>
      <IconButton
        icon={Delete24Filled}
        description="Delete task"
        onClick={() => setDeleteModalOpen(true)}
      />
      <ConfirmationModal
        open={deleteModalOpen}
        closeModal={() => setDeleteModalOpen(false)}
        confirmAction={() => {
          setDeleteModalOpen(false)
          deleteTask(taskId)
        }}
        title="Confirm Deletion"
        content="This task will be deleted"
        confirmText="Delete"
      />
    </>
  )
}
