import { useQuery  } from '@tanstack/react-query'
import { useClientFetch } from '@/infra/lib/useClientFetch'
import { makeGetWorkSummary } from '@/infra/workSummary/getWorkSummary'
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser'
import { WorkSummary } from '@/domain/WorkSummary'

export const useGetWorkSummary = () => {
  const apiClient = useClientFetch()
  const { id: userId } = useGetCurrentUser()
  const getWorkSummary = makeGetWorkSummary(apiClient)

  const { data } = useQuery({
    // @ts-expect-error: Not sure why this fails
    queryKey: ['workSummary', userId],
    queryFn: () => getWorkSummary(),
    initialData: []
  })
  if (!data) {
    return {} as WorkSummary
  }
  return data
}
