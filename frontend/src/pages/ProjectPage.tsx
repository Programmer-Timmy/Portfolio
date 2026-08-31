import { useState } from 'react'
import { useParams } from 'react-router-dom'
import { Container } from '@/components/ui/Container'
import { Card } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { Button } from '@/components/ui/Button'
import { Skeleton } from '@/components/ui/Skeleton'
import { Icon } from '@/components/ui/Icon'
import { AppLink } from '@/components/ui/AppLink'
import { ProjectImage } from '@/components/ProjectImage'
import { DeltaContent } from '@/components/DeltaContent'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import { cn } from '@/lib/cn'
import type { ApiImage, ProjectDetail } from '@/lib/types'

function formatDate(iso: string | null): string | null {
  if (!iso) return null
  const date = new Date(iso)
  if (Number.isNaN(date.getTime())) return null
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

export function ProjectPage() {
  const { id } = useParams<{ id: string }>()
  const query = useApi<ProjectDetail>(`/projects/${id}`)
  useDocumentTitle(query.status === 'success' ? query.data.name : 'Project')

  return (
    <Container as="article" className="py-14 sm:py-20">
      <AppLink
        to="/projects"
        className="inline-flex items-center gap-1.5 text-sm font-medium text-teal hover:underline"
      >
        <Icon name="fa-solid fa-arrow-left" />
        All projects
      </AppLink>

      {query.status === 'loading' && <ProjectSkeleton />}

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
            <Button to="/projects" size="sm">
              All projects
            </Button>
          </div>
        </div>
      )}

      {query.status === 'success' && <ProjectView project={query.data} />}
    </Container>
  )
}

function ProjectView({ project }: { project: ProjectDetail }) {
  const images = [project.image, ...project.gallery].filter(
    (image): image is ApiImage => Boolean(image),
  )

  return (
    <div className="mt-6">
      <header className="max-w-3xl">
        {project.flags.inProgress && (
          <Badge tone="degraded" className="mb-3 gap-1.5">
            <Icon name="fa-solid fa-person-digging" />
            Work in progress
          </Badge>
        )}
        <h1 className="text-h1">{project.name}</h1>
      </header>

      <div className="mt-8 grid gap-10 lg:grid-cols-[1.7fr_1fr] lg:items-start">
        <div>
          {images.length > 0 && <Gallery images={images} name={project.name} />}

          {project.description && project.description.length > 0 ? (
            <DeltaContent ops={project.description} className="mt-8" />
          ) : project.excerpt ? (
            <p className="mt-8 text-ink-secondary">{project.excerpt}</p>
          ) : null}
        </div>

        <aside className="lg:sticky lg:top-24">
          <Card className="space-y-5 p-6">
            {(project.links.live || project.links.repository) && (
              <div className="flex flex-col gap-2">
                {project.links.live && (
                  <Button href={project.links.live}>
                    <Icon name="fa-solid fa-arrow-up-right-from-square" />
                    Visit project
                  </Button>
                )}
                {project.links.repository && (
                  <Button href={project.links.repository} variant="secondary">
                    <Icon name="fa-brands fa-github" />
                    View on GitHub
                  </Button>
                )}
              </div>
            )}

            <Meta label="Created" value={formatDate(project.createdAt)} />
            <Meta label="Last updated" value={formatDate(project.updatedAt)} />

            {project.languages.length > 0 && (
              <div>
                <p className="text-xs font-medium uppercase tracking-wide text-ink-muted">
                  Built with
                </p>
                <ul className="mt-2 flex flex-wrap gap-1.5">
                  {project.languages.map((lang) => (
                    <li
                      key={lang.name}
                      className="inline-flex items-center gap-1.5 rounded-full border border-line px-2 py-0.5 text-xs text-ink-secondary"
                    >
                      <span
                        className="h-2 w-2 rounded-full"
                        style={{
                          backgroundColor: lang.color ?? 'var(--color-ink-muted)',
                        }}
                      />
                      {lang.name}
                    </li>
                  ))}
                </ul>
              </div>
            )}

            {project.contributors.length > 0 && (
              <div>
                <p className="text-xs font-medium uppercase tracking-wide text-ink-muted">
                  Contributors
                </p>
                <ul className="mt-2 flex flex-wrap gap-2">
                  {project.contributors.map((person, idx) => (
                    <li key={person.login ?? person.profileUrl ?? idx}>
                      <a
                        href={person.profileUrl ?? undefined}
                        target="_blank"
                        rel="noopener noreferrer"
                        title={person.login ?? undefined}
                        className="block"
                      >
                        {person.avatarUrl ? (
                          <img
                            src={person.avatarUrl}
                            alt={person.login ?? 'Contributor'}
                            loading="lazy"
                            className="h-9 w-9 rounded-full border border-line"
                          />
                        ) : (
                          <span className="flex h-9 w-9 items-center justify-center rounded-full border border-line text-xs text-ink-muted">
                            {(person.login ?? '?').slice(0, 2)}
                          </span>
                        )}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </Card>
        </aside>
      </div>
    </div>
  )
}

function Meta({ label, value }: { label: string; value: string | null }) {
  if (!value) return null
  return (
    <div>
      <p className="text-xs font-medium uppercase tracking-wide text-ink-muted">
        {label}
      </p>
      <p className="mt-0.5 text-sm text-ink-secondary">{value}</p>
    </div>
  )
}

function Gallery({ images, name }: { images: ApiImage[]; name: string }) {
  const [active, setActive] = useState(0)
  const current = images[Math.min(active, images.length - 1)]

  return (
    <div>
      <div className="aspect-[16/10] overflow-hidden rounded-card border border-line">
        <ProjectImage image={current} alt={name} className="h-full w-full" />
      </div>

      {images.length > 1 && (
        <ul className="mt-3 flex flex-wrap gap-2">
          {images.map((image, idx) => (
            <li key={idx}>
              <button
                type="button"
                onClick={() => setActive(idx)}
                aria-label={`Show image ${idx + 1} of ${images.length}`}
                aria-current={idx === active}
                className={cn(
                  'block overflow-hidden rounded-md border transition-colors',
                  idx === active
                    ? 'border-teal'
                    : 'border-line hover:border-teal/50',
                )}
              >
                <ProjectImage image={image} alt="" className="h-14 w-20" />
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  )
}

function ProjectSkeleton() {
  return (
    <div className="mt-6">
      <Skeleton className="h-11 w-2/3" />
      <div className="mt-8 grid gap-10 lg:grid-cols-[1.7fr_1fr]">
        <div className="space-y-4">
          <Skeleton className="aspect-[16/10] w-full rounded-card" />
          <Skeleton className="h-5 w-full" />
          <Skeleton className="h-5 w-5/6" />
          <Skeleton className="h-5 w-4/6" />
        </div>
        <Skeleton className="h-64 w-full rounded-card" />
      </div>
    </div>
  )
}
