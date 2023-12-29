'use server'

import { BaseError } from '@/_lib/errors/BaseError'
import { Task } from '@/domain/Task'
import { TemplateIntent } from '@/domain/Template'
import { serverFetch } from '@/infra/lib/serverFetch'
import { makeCreateTemplate } from '@/infra/template/createTemplate'
import { revalidateTag } from 'next/cache'

export const createTemplate = async (task: Task, formData: FormData) => {
  const newTemplate = {
    ...task,
    isGlobal: false,
    name: formData.get('name')
  } as TemplateIntent

  const apiClient = await serverFetch()
  const createTemplate = makeCreateTemplate(apiClient)

  try {
    const template = await createTemplate(newTemplate)

    revalidateTag('templates')

    return { data: template }
  } catch (e) {
    if (e instanceof BaseError) {
      return { error: e.message }
    }

    throw e
  }
}
