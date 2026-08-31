import { Container } from '@/components/ui/Container'
import { PageHeading } from '@/components/ui/PageHeading'
import { Button } from '@/components/ui/Button'
import { Skeleton } from '@/components/ui/Skeleton'
import { Icon } from '@/components/ui/Icon'
import { ProjectCard } from '@/components/ProjectCard'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import type { ProjectSummary } from '@/lib/types'
import { projects } from '@/content/projects'

export function ProjectsPage() {
  useDocumentTitle('Projects')

  const query = useApi<ProjectSummary[]>('/projects')

  return (
    <>
      <PageHeading eyebrow={projects.eyebrow} title={projects.title}>
        {projects.lead}
      </PageHeading>

      <Container as="section" className="pb-24">
        <div className="mb-8">
          <Button href={projects.source.url} variant="secondary" size="sm">
            <Icon name="fa-brands fa-github" />
            {projects.source.label}
          </Button>
        </div>

        {query.status === 'loading' && (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }, (_, i) => (
              <Skeleton key={i} className="h-72 w-full rounded-card" />
            ))}
          </div>
        )}

        {query.status === 'error' && (
          <div className="rounded-card border border-line bg-surface p-8 text-center">
            <p className="font-medium text-ink">{projects.errorTitle}</p>
            <p className="mt-1 text-sm text-ink-secondary">{query.error.message}</p>
            <div className="mt-4 flex justify-center">
              <Button size="sm" variant="secondary" onClick={query.reload}>
                Try again
              </Button>
            </div>
          </div>
        )}

        {query.status === 'success' && query.data.length === 0 && (
          <p className="text-ink-secondary">{projects.empty}</p>
        )}

        {query.status === 'success' && query.data.length > 0 && (
          <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {query.data.map((project) => (
              <li key={project.id}>
                <ProjectCard project={project} />
              </li>
            ))}
          </ul>
        )}
      </Container>
    </>
  )
}
