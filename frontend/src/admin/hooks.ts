import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { adminApi, clearCsrfToken } from './lib/adminApi'
import { isGitHubRepoUrl } from './lib/github'
import { qk } from './lib/queryKeys'
import type {
  AdminSession,
  AdminStats,
  GitHubContributor,
  GitHubLanguages,
  GitHubRepo,
  GitHubUser,
  LanguageOption,
  LoginResult,
  ProjectAdminRow,
} from './lib/types'

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

export function useAdminProjects(includeRemoved: boolean) {
  return useQuery({
    queryKey: [...qk.projects, { includeRemoved }],
    queryFn: () =>
      adminApi.get<ProjectAdminRow[]>(
        `/admin/projects${includeRemoved ? '?includeRemoved=1' : ''}`,
      ),
  })
}

export function useLanguageOptions() {
  return useQuery({
    queryKey: qk.languages,
    queryFn: () => adminApi.get<LanguageOption[]>('/admin/languages'),
    staleTime: 60 * 60_000,
  })
}

function useProjectMutation(fn: (id: number) => Promise<unknown>) {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: fn,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: qk.projects })
      qc.invalidateQueries({ queryKey: qk.stats })
    },
  })
}

export function useDeleteProject() {
  return useProjectMutation((id) => adminApi.del<null>(`/admin/projects/${id}`))
}

export function useRestoreProject() {
  return useProjectMutation((id) => adminApi.post<null>(`/admin/projects/${id}/restore`))
}

// --- GitHub proxy -----------------------------------------------------------

const q = (url: string) => encodeURIComponent(url.trim())

export function useGitHubRepo(url: string) {
  return useQuery({
    queryKey: ['admin', 'github', 'repo', url],
    queryFn: () => adminApi.get<GitHubRepo>(`/admin/github/repo?url=${q(url)}`),
    enabled: isGitHubRepoUrl(url),
    staleTime: 5 * 60_000,
    retry: false,
  })
}

export function useGitHubLanguages(url: string, enabled: boolean) {
  return useQuery({
    queryKey: ['admin', 'github', 'languages', url],
    queryFn: () => adminApi.get<GitHubLanguages>(`/admin/github/languages?url=${q(url)}`),
    enabled: enabled && isGitHubRepoUrl(url),
    staleTime: 5 * 60_000,
    retry: false,
  })
}

export function useGitHubContributors(url: string, enabled: boolean) {
  return useQuery({
    queryKey: ['admin', 'github', 'contributors', url],
    queryFn: () => adminApi.get<GitHubContributor[]>(`/admin/github/contributors?url=${q(url)}`),
    enabled: enabled && isGitHubRepoUrl(url),
    staleTime: 5 * 60_000,
    retry: false,
  })
}

/** Imperative lookup for "add a contributor by GitHub username". */
export function useGitHubUserLookup() {
  return useMutation({
    mutationFn: (login: string) =>
      adminApi.get<GitHubUser>(`/admin/github/user?login=${encodeURIComponent(login.trim())}`),
  })
}
