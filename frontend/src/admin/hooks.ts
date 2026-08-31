import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { adminApi, clearCsrfToken } from './lib/adminApi'
import { qk } from './lib/queryKeys'
import type { AdminSession, AdminStats, LoginResult } from './lib/types'

export function useSession() {
  return useQuery({
    queryKey: qk.session,
    queryFn: () => adminApi.get<AdminSession>('/auth/session'),
    staleTime: 60_000,
  })
}

export function useLogin() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (body: { username: string; password: string }) =>
      adminApi.post<LoginResult>('/auth/login', body),
    onSuccess: (result) => {
      qc.setQueryData<AdminSession>(qk.session, {
        authenticated: result.authenticated,
        admin: result.admin,
      })
    },
  })
}

export function useLogout() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: () => adminApi.post<null>('/auth/logout'),
    onSuccess: () => {
      clearCsrfToken()
      qc.setQueryData<AdminSession>(qk.session, { authenticated: false, admin: false })
      qc.removeQueries({ queryKey: ['admin'], predicate: (q) => q.queryKey[1] !== 'session' })
    },
  })
}

export function useStats() {
  return useQuery({
    queryKey: qk.stats,
    queryFn: () => adminApi.get<AdminStats>('/admin/stats'),
  })
}
