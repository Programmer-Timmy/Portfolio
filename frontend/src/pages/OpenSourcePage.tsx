import { Link } from 'react-router-dom'
import { Container } from '@/components/ui/Container'
import { PageHeading } from '@/components/ui/PageHeading'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { Card } from '@/components/ui/Card'
import { Skeleton } from '@/components/ui/Skeleton'
import { Icon } from '@/components/ui/Icon'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import type { OpenSourceProject } from '@/lib/types'
import { openSource } from '@/content/opensource'

export function OpenSourcePage() {
  useDocumentTitle('Open source')

  const query = useApi<OpenSourceProject[]>('/opensource')

  return (
    <>
      <PageHeading eyebrow={openSource.eyebrow} title={openSource.title}>
        {openSource.lead}
      </PageHeading>

      <Container as="section" className="pb-24">
        <div className="mb-8">
          <Button href={openSource.source.url} variant="secondary" size="sm">
            <Icon name="fa-brands fa-github" />
            {openSource.source.label}
          </Button>
        </div>

        {query.status === 'loading' && (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }, (_, i) => (
              <Skeleton key={i} className="h-44 w-full rounded-card" />
            ))}
          </div>
        )}

        {query.status === 'error' && (
          <div className="rounded-card border border-line bg-surface p-8 text-center">
            <p className="font-medium text-ink">{openSource.errorTitle}</p>
            <p className="mt-1 text-sm text-ink-secondary">{query.error.message}</p>
            <div className="mt-4 flex justify-center">
              <Button size="sm" variant="secondary" onClick={query.reload}>
                Try again
              </Button>
            </div>
          </div>
        )}

        {query.status === 'success' && query.data.length === 0 && (
          <p className="text-ink-secondary">{openSource.empty}</p>
        )}

        {query.status === 'success' && query.data.length > 0 && (
          <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {query.data.map((project) => (
              <li key={project.id}>
                <RepoCard project={project} />
              </li>
            ))}
          </ul>
        )}
      </Container>
    </>
  )
}

function RepoCard({ project }: { project: OpenSourceProject }) {
  return (
    <Card interactive className="group relative flex h-full flex-col p-5">
      <h2 className="font-heading text-lg font-semibold">
        <Link
          to={`/opensource/${project.id}`}
          className="after:absolute after:inset-0 after:content-[''] hover:text-teal"
        >
          <Icon name="fa-brands fa-github" className="mr-2 text-ink-muted" />
          {project.name}
        </Link>
      </h2>

      {project.description && (
        <p className="mt-2 line-clamp-3 flex-1 text-sm text-ink-secondary">
          {project.description}
        </p>
      )}

      <div className="mt-4 flex items-center justify-between">
        {project.pullRequestCount !== null && (
          <Badge tone="brand">
            <Icon name="fa-solid fa-code-branch" className="mr-1" />
            {project.pullRequestCount}{' '}
            {project.pullRequestCount === 1 ? 'PR' : 'PRs'}
          </Badge>
        )}
        {project.repositoryUrl && (
          <a
            href={project.repositoryUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="relative z-10 text-sm font-medium text-teal hover:underline"
          >
            Repository →
          </a>
        )}
      </div>
    </Card>
  )
}
