import { Container } from '@/components/ui/Container'
import { Section } from '@/components/ui/Section'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { Card } from '@/components/ui/Card'
import { Skeleton } from '@/components/ui/Skeleton'
import { ProjectCard } from '@/components/ProjectCard'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import type { OpenSourceProject, Profile, ProjectSummary } from '@/lib/types'

export function HomePage() {
  useDocumentTitle()
  const profile = useApi<Profile>('/profile')
  const featured = useApi<ProjectSummary[]>('/projects?featured=true')
  const openSource = useApi<OpenSourceProject[]>('/opensource')

  return (
    <>
      <Hero profile={profile.data} loading={profile.status === 'loading'} />

      <Section
        eyebrow="Selected work"
        title="Featured projects"
        description="A few things I've built recently. Browse the rest for the full picture."
        action={{ label: 'All projects', to: '/projects' }}
        muted
      >
        {featured.status === 'loading' && (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {[0, 1, 2].map((i) => (
              <Skeleton key={i} className="h-72 w-full rounded-card" />
            ))}
          </div>
        )}
        {featured.status === 'error' && (
          <p className="text-ink-secondary">
            Projects couldn't be loaded right now.
          </p>
        )}
        {featured.status === 'success' && (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {featured.data.map((project) => (
              <ProjectCard key={project.id} project={project} />
            ))}
          </div>
        )}
      </Section>

      <SkillsSection profile={profile.data} />

      <OpenSourceStrip
        projects={openSource.status === 'success' ? openSource.data : []}
      />

      <ScoutingSection profile={profile.data} />

      <Section className="pb-24">
        <Card className="flex flex-col items-start gap-6 p-8 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 className="text-h3">Working on something for your group or team?</h2>
            <p className="mt-2 max-w-xl text-ink-secondary">
              I take on web and tooling projects, especially for Scouting and
              volunteer organisations. Tell me what you need.
            </p>
          </div>
          <Button to="/contact" size="lg" className="shrink-0">
            Get in touch
          </Button>
        </Card>
      </Section>
    </>
  )
}

function Hero({ profile, loading }: { profile?: Profile; loading: boolean }) {
  return (
    <Container as="section" className="py-20 sm:py-28">
      <div className="grid items-center gap-12 lg:grid-cols-[1.4fr_1fr]">
        <div>
          <Badge>{profile?.headline ?? 'Web development & technology'}</Badge>
          <h1 className="mt-5 max-w-2xl text-h1">
            {profile?.summary ??
              "I build practical software for clients, in the open, and for Scouting."}
          </h1>
          <p className="mt-5 max-w-xl text-lg text-ink-secondary">
            I'm {profile?.name ?? 'Tim van der Kloet'}
            {profile?.location ? `, based in ${profile.location}` : ''}. I care
            about clean, maintainable code and software that actually helps the
            people using it.
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <Button to="/projects">View projects</Button>
            <Button to="/contact" variant="secondary">
              Get in touch
            </Button>
          </div>
        </div>

        <Card className="p-6">
          <p className="font-mono text-xs uppercase tracking-widest text-teal">
            Currently
          </p>
          {loading && (
            <div className="mt-4 space-y-3">
              <Skeleton className="h-5 w-4/5" />
              <Skeleton className="h-5 w-3/5" />
            </div>
          )}
          {profile && (
            <ul className="mt-4 space-y-4">
              {profile.roles.map((role) => (
                <li key={role.title}>
                  <p className="font-medium">{role.title}</p>
                  <p className="text-sm text-ink-secondary">{role.organization}</p>
                </li>
              ))}
              <li>
                <p className="font-medium">{profile.scouting.role}</p>
                <p className="text-sm text-ink-secondary">
                  {profile.scouting.group}
                  {profile.scouting.years
                    ? ` · ${profile.scouting.years} years`
                    : ''}
                </p>
              </li>
            </ul>
          )}
        </Card>
      </div>
    </Container>
  )
}

function SkillsSection({ profile }: { profile?: Profile }) {
  if (!profile) return null
  return (
    <Section eyebrow="Toolbox" title="What I work with">
      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {profile.skills.map((group) => (
          <div key={group.group}>
            <h3 className="font-heading text-sm font-semibold text-ink-secondary">
              {group.group}
            </h3>
            <ul className="mt-3 flex flex-wrap gap-2">
              {group.items.map((item) => (
                <li
                  key={item}
                  className="rounded-full border border-line bg-surface px-2.5 py-1 text-xs"
                >
                  {item}
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
    </Section>
  )
}

function OpenSourceStrip({ projects }: { projects: OpenSourceProject[] }) {
  const prTotal = projects.reduce((sum, p) => sum + (p.pullRequestCount ?? 0), 0)

  return (
    <Section
      eyebrow="In the open"
      title="Open source"
      description="I contribute back to the tools I use."
      action={{ label: 'All contributions', to: '/opensource' }}
      muted
    >
      <div className="flex flex-wrap gap-4">
        <Card className="px-6 py-5">
          <p className="font-heading text-3xl font-extrabold text-teal">
            {projects.length}
          </p>
          <p className="text-sm text-ink-secondary">repositories</p>
        </Card>
        <Card className="px-6 py-5">
          <p className="font-heading text-3xl font-extrabold text-teal">
            {prTotal}
          </p>
          <p className="text-sm text-ink-secondary">merged / open PRs</p>
        </Card>
        {projects.slice(0, 3).map((project) => (
          <Card key={project.id} className="flex-1 px-6 py-5">
            <p className="font-mono text-sm">{project.name}</p>
            {project.description && (
              <p className="mt-1 line-clamp-2 text-sm text-ink-secondary">
                {project.description}
              </p>
            )}
          </Card>
        ))}
      </div>
    </Section>
  )
}

function ScoutingSection({ profile }: { profile?: Profile }) {
  if (!profile) return null
  return (
    <Section>
      <Card className="grid gap-6 p-8 md:grid-cols-[1fr_1.4fr] md:items-center">
        <div>
          <Badge>Scouting</Badge>
          <h2 className="mt-4 text-h2">{profile.scouting.group}</h2>
          <p className="mt-1 text-ink-secondary">
            {profile.scouting.role}
            {profile.scouting.years ? ` · ${profile.scouting.years} years` : ''}
          </p>
        </div>
        <div className="space-y-4 text-ink-secondary">
          <p>{profile.scouting.blurb}</p>
          <a
            href={profile.scouting.groupUrl}
            target="_blank"
            rel="noreferrer"
            className="inline-block text-sm font-medium text-teal hover:underline"
          >
            Visit {profile.scouting.group} →
          </a>
        </div>
      </Card>
    </Section>
  )
}
