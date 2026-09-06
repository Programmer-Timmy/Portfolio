import { useParams } from 'react-router-dom'
import { Container } from '@/components/ui/Container'
import { Card } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { Button } from '@/components/ui/Button'
import { Skeleton } from '@/components/ui/Skeleton'
import { Icon } from '@/components/ui/Icon'
import { AppLink } from '@/components/ui/AppLink'
import { TextLink } from '@/components/ui/TextLink'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import type { BadgeTone } from '@/components/ui/Badge'
import type { OpenSourceDetail, PullRequest } from '@/lib/types'
import { openSource } from '@/content/opensource'

const STATUS_TONE: Record<string, BadgeTone> = {
  merged: 'brand',
  open: 'up',
  closed: 'down',
}

function formatDate(iso: string | null): string | null {
  if (!iso) return null
  const date = new Date(iso)
  if (Number.isNaN(date.getTime())) return null
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

export function OpenSourceProjectPage() {
  const { id } = useParams<{ id: string }>()
  const query = useApi<OpenSourceDetail>(`/opensource/${id}`)
  useDocumentTitle(query.status === 'success' ? query.data.name : 'Open source')

  return (
    <Container as="article" className="py-14 sm:py-20">
      <AppLink
        to="/opensource"
        className="inline-flex items-center gap-1.5 text-sm font-medium text-teal hover:underline"
      >
        <Icon name="fa-solid fa-arrow-left" />
        {openSource.detail.backLabel}
      </AppLink>

      {query.status === 'loading' && (
        <div className="mt-6 space-y-4">
          <Skeleton className="h-10 w-2/3" />
          <Skeleton className="h-24 w-full rounded-card" />
          <Skeleton className="h-24 w-full rounded-card" />
        </div>
      )}

      {query.status === 'error' && (
        <div className="mt-10 rounded-card border border-line bg-surface p-8 text-center">
          <p className="text-ink-secondary">
            {query.error.status === 404
              ? "That project doesn't exist."
              : query.error.message}
          </p>
          <div className="mt-4 flex justify-center gap-3">
            {query.error.status !== 404 && (
              <Button size="sm" variant="secondary" onClick={query.reload}>
                Try again
              </Button>
            )}
            <Button to="/opensource" size="sm">
              {openSource.detail.backLabel}
            </Button>
          </div>
        </div>
      )}

      {query.status === 'success' && <RepoView project={query.data} />}
    </Container>
  )
}

function RepoView({ project }: { project: OpenSourceDetail }) {
  return (
    <div className="mt-6">
      <h1 className="text-h1">
        <Icon name="fa-brands fa-github" className="mr-3 text-ink-muted" />
        {project.name}
      </h1>

      <div className="mt-8 grid gap-10 lg:grid-cols-[1.7fr_1fr] lg:items-start">
        <div>
          <h2 className="text-h3">{openSource.detail.contributionsHeading}</h2>

          {project.pullRequests.length > 0 ? (
            <ul className="mt-4 space-y-3">
              {project.pullRequests.map((pr) => (
                <li key={pr.id}>
                  <PullRequestCard pr={pr} />
                </li>
              ))}
            </ul>
          ) : (
            <p className="mt-4 text-ink-secondary">
              {openSource.detail.noContributions}
            </p>
          )}
        </div>

        <aside className="lg:sticky lg:top-24">
          <Card className="space-y-4 p-6">
            <p className="font-mono text-xs uppercase tracking-widest text-teal">
              {openSource.detail.aboutHeading}
            </p>
            {project.description && (
              <p className="text-sm text-ink-secondary">{project.description}</p>
            )}
            {project.repositoryUrl && (
              <Button href={project.repositoryUrl} variant="secondary">
                <Icon name="fa-brands fa-github" />
                {openSource.detail.repoButton}
              </Button>
            )}
          </Card>
        </aside>
      </div>
    </div>
  )
}

function PullRequestCard({ pr }: { pr: PullRequest }) {
  const tone = STATUS_TONE[pr.status.toLowerCase()] ?? 'maintenance'
  const date = formatDate(pr.createdAt)

  return (
    <Card className="p-4">
      <div className="flex items-start justify-between gap-3">
        <h3 className="font-medium">
          <TextLink to={pr.url}>
            <Icon name="fa-solid fa-code-branch" className="mr-2 text-ink-muted" />
            {pr.title}
          </TextLink>
        </h3>
        {date && (
          <span className="shrink-0 text-xs text-ink-muted">{date}</span>
        )}
      </div>
      {pr.description && (
        <p className="mt-2 line-clamp-3 text-sm text-ink-secondary">
          {pr.description}
        </p>
      )}
      <div className="mt-3">
        <Badge tone={tone}>{pr.status}</Badge>
      </div>
    </Card>
  )
}
