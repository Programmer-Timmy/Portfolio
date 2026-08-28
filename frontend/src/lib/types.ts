/** Shapes returned by the PHP REST API (private/api/Support/Resource.php). */

export type ApiImage = {
  src: string
  webp?: string
  srcset?: string
  width?: number
  height?: number
}

export type Language = {
  name: string
  color: string | null
  percentage: number | null
}

export type Contributor = {
  login: string | null
  avatarUrl: string | null
  profileUrl: string | null
  contributions: number
}

export type ProjectLinks = {
  repository: string | null
  live: string | null
}

export type ProjectFlags = {
  pinned: boolean
  inProgress: boolean
  privateRepo: boolean
}

export type ProjectSummary = {
  id: number
  name: string
  image: ApiImage | null
  languages: Language[]
  links: ProjectLinks
  flags: ProjectFlags
  createdAt: string | null
  updatedAt: string | null
}

/** Quill delta op. Description is stored as a delta. */
export type DeltaOp = {
  insert?: string | Record<string, unknown>
  attributes?: Record<string, unknown>
}

export type ProjectDetail = ProjectSummary & {
  description: DeltaOp[] | null
  excerpt: string | null
  gallery: ApiImage[]
  contributors: Contributor[]
}

export type OpenSourceProject = {
  id: number
  name: string
  description: string | null
  repositoryUrl: string | null
  pullRequestCount: number | null
}

export type PullRequest = {
  id: number
  title: string
  url: string
  status: string
  description: string | null
  createdAt: string | null
}

export type OpenSourceDetail = OpenSourceProject & {
  pullRequests: PullRequest[]
}

export type Video = {
  id: number
  title: string
  youtubeId: string
  url: string
  embedUrl: string
  thumbnailUrl: string
  pinned: boolean
  publishedAt: string | null
}

export type Social = {
  label: string
  handle: string
  url: string
  icon: string
}

export type SkillGroup = {
  group: string
  items: string[]
}

export type Client = {
  name: string
  url: string
  /** Relationship badge, e.g. "Volunteer", "Client", "Freelance". */
  kind: string
  summary: string
  tags: string[]
}

export type Profile = {
  name: string
  headline: string
  summary: string
  location: string
  birthDate: string
  timezone: string
  age: number | null
  roles: { title: string; organization: string; current: boolean }[]
  clients: Client[]
  bio: string[]
  cv: { label: string; url: string }
  socials: Social[]
  skills: SkillGroup[]
}
