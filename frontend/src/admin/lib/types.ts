/** Shapes returned by the admin + auth API. */

export type AdminSession = {
  authenticated: boolean
  admin: boolean
}

export type LoginResult = {
  authenticated: boolean
  admin: boolean
  /** Path the user was heading to before being bounced to login, if any. */
  redirect: string | null
}

export type AdminImage = {
  src: string
  webp?: string
  srcset?: string
  width?: number
  height?: number
}

export type ProjectFlags = {
  pinned: boolean
  inProgress: boolean
  privateRepo: boolean
}

export type ProjectAdminRow = {
  id: number
  name: string
  image: AdminImage | null
  links: { repository: string | null; live: string | null }
  flags: ProjectFlags
  removed: boolean
  createdAt: string | null
  updatedAt: string | null
}

export type LanguageOption = {
  id: number
  name: string
  color: string | null
}

export type AdminStats = {
  projects: number
  projectsPinned: number
  projectsInProgress: number
  videos: number
  openSourceProjects: number
  pullRequests: number
}
