/** Central registry of admin query keys, so invalidations stay consistent. */
export const qk = {
  session: ['admin', 'session'] as const,
  stats: ['admin', 'stats'] as const,
  projects: ['admin', 'projects'] as const,
  project: (id: string | number) => ['admin', 'projects', String(id)] as const,
  languages: ['admin', 'languages'] as const,
  videos: ['admin', 'videos'] as const,
  openSource: ['admin', 'opensource'] as const,
}
