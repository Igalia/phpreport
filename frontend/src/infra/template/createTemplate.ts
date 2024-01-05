import { Template, TemplateIntent } from '@/domain/Template'
import { ApiClient } from '../lib/apiClient'
import { BaseError } from '@/_lib/errors/BaseError'

const createTemplateError = (message = 'Failed to create template') => {
  const name = 'CreateTemplateError'

  return new BaseError({ message, name, code: name })
}

export const makeCreateTemplate = (apiClient: ApiClient) => async (template: TemplateIntent) => {
  const validation = TemplateIntent.safeParse(template)

  if (!validation.success) {
    throw createTemplateError(
      validation.error.issues.reduce((acc, nextValue) => `${acc} ${nextValue.message}`, '')
    )
  }

  try {
    const response = await apiClient('/v1/timelog/templates', {
      method: 'POST',
      body: JSON.stringify(template)
    })

    if (!response.ok) {
      throw createTemplateError()
    }

    return (await response.json()) as Template
  } catch (e) {
    throw createTemplateError()
  }
}
