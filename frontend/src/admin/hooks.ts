import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { adminApi, clearCsrfToken } from './lib/adminApi'
import { isGitHubRepoUrl } from './lib/github'
import { qk } from './lib/queryKeys'
import type { ProjectFormOutput } from './lib/schemas'
import type {
  AdminSession,
  AdminStats,
  AdminVideo,
  GitHubContributor,
  GitHubLanguages,
  GitHubRepo,
  GitHubUser,
  LanguageOption,
  LoginResult,
  ProjectAdminRow,
  ProjectEditable,
  VideoSyncSummary,
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

export function useProject(id: string | undefined) {
  return useQuery({
    queryKey: qk.project(id ?? 'new'),
    queryFn: () => adminApi.get<ProjectEditable>(`/admin/projects/${id}`),
    enabled: !!id && id !== 'new',
  })
}

function buildProjectFormData(values: ProjectFormOutput): FormData {
  const fd = new FormData()
  fd.append(
    'payload',
    JSON.stringify({
      name: values.name,
      link: values.link,
      github: values.github,
      pinned: values.pinned,
      inProgress: values.inProgress,
      privateRepo: values.privateRepo,
      description: values.description,
      languages: values.languages,
      contributors: values.contributors,
    }),
  )
  fd.append(
    'imageState',
    JSON.stringify({ images: values.existingImages, removed: values.removedImages }),
  )
  for (const file of values.newFiles) {
    fd.append('images[]', file)
  }
  return fd
}

export function useCreateProject() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (values: ProjectFormOutput) =>
      adminApi.postForm<ProjectEditable>('/admin/projects', buildProjectFormData(values)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: qk.projects })
      qc.invalidateQueries({ queryKey: qk.stats })
    },
  })
}

export function useUpdateProject(id: string) {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (values: ProjectFormOutput) =>
      adminApi.postForm<ProjectEditable>(`/admin/projects/${id}`, buildProjectFormData(values)),
    onSuccess: (data) => {
      qc.invalidateQueries({ queryKey: qk.projects })
      qc.invalidateQueries({ queryKey: qk.stats })
      qc.setQueryData(qk.project(id), data)
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

// --- videos ----------------------------------------------------------------

export function useAdminVideos(includeDeleted: boolean) {
  return useQuery({
    queryKey: [...qk.videos, { includeDeleted }],
    queryFn: () =>
      adminApi.get<AdminVideo[]>(
        `/admin/videos${includeDeleted ? '?includeDeleted=1' : ''}`,
      ),
  })
}

export function useSyncVideos() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: () => adminApi.post<VideoSyncSummary>('/admin/videos/sync'),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: qk.videos })
      qc.invalidateQueries({ queryKey: qk.stats })
    },
  })
}

export function useToggleVideoPin() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (video: AdminVideo) => adminApi.post<AdminVideo>(`/admin/videos/${video.id}/pin`),
    onMutate: async (video) => {
      await qc.cancelQueries({ queryKey: qk.videos })
      const prev = qc.getQueriesData<AdminVideo[]>({ queryKey: qk.videos })
      qc.setQueriesData<AdminVideo[]>({ queryKey: qk.videos }, (old) =>
        old?.map((v) => (v.id === video.id ? { ...v, pinned: !v.pinned } : v)),
      )
      return { prev }
    },
    onError: (_err, _video, ctx) => {
      ctx?.prev.forEach(([key, data]) => qc.setQueryData(key, data))
    },
    onSettled: () => qc.invalidateQueries({ queryKey: qk.videos }),
  })
}

function useVideoMutation<T>(fn: (id: number) => Promise<T>) {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: fn,
    onSuccess: () => qc.invalidateQueries({ queryKey: qk.videos }),
  })
}

export function useRenameVideo() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (args: { id: number; title: string }) =>
      adminApi.patch<AdminVideo>(`/admin/videos/${args.id}`, { title: args.title }),
    onSuccess: () => qc.invalidateQueries({ queryKey: qk.videos }),
  })
}

export function useDeleteVideo() {
  return useVideoMutation((id) => adminApi.del<null>(`/admin/videos/${id}`))
}

export function useRestoreVideo() {
  return useVideoMutation((id) => adminApi.post<null>(`/admin/videos/${id}/restore`))
}
