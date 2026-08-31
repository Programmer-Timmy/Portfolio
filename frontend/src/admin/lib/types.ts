/** Shapes returned by the admin + auth API. */

import type { DeltaOp } from '@/lib/types'
export type { DeltaOp }

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

export type GitHubRepo =
  | { exists: false }
  | {
      exists: true
      fullName: string
      private: boolean
      description: string | null
      htmlUrl: string | null
      defaultBranch: string | null
    }

export type GitHubLanguage = {
  programmingLanguageId: number
  name: string
  color: string | null
  percentage: number
}

export type GitHubLanguages = {
  languages: GitHubLanguage[]
  unmapped: string[]
}

export type GitHubContributor = {
  id: number
  login: string | null
  avatarUrl: string | null
  profileUrl: string | null
  contributions: number
}

export type GitHubUser = {
  id: number
  login: string
  avatarUrl: string | null
  profileUrl: string | null
}

export type ProjectLanguage = {
  programmingLanguageId: number
  name: string
  color: string | null
  percentage: number | null
}

export type ProjectContributor = {
  id: number
  login: string | null
  avatarUrl: string | null
  profileUrl: string | null
  contributions: number
}

export type ProjectEditable = {
  id: number
  name: string
  link: string
  github: string
  description: DeltaOp[] | null
  flags: ProjectFlags
  languages: ProjectLanguage[]
  contributors: ProjectContributor[]
  imagePaths: string[]
  images: AdminImage[]
}

export type AdminStats = {
  projects: number
  projectsPinned: number
  projectsInProgress: number
  videos: number
  openSourceProjects: number
  pullRequests: number
}
