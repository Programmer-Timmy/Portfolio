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

export type AdminStats = {
  projects: number
  projectsPinned: number
  projectsInProgress: number
  videos: number
  openSourceProjects: number
  pullRequests: number
}
