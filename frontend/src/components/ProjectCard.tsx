import { Link } from 'react-router-dom'
import type { ProjectSummary } from '@/lib/types'
import { Card } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { Icon } from '@/components/ui/Icon'
import { ProjectImage } from './ProjectImage'

export function ProjectCard({ project }: { project: ProjectSummary }) {
  const languages = project.languages.slice(0, 3)

  return (
    <Card interactive className="group relative flex flex-col overflow-hidden h-full">
      <div className="relative aspect-[16/10] overflow-hidden border-b border-line">
        <ProjectImage
          image={project.image}
          alt={project.name}
          className="h-full w-full transition-transform duration-300 group-hover:scale-[1.03]"
        />
        {project.flags.inProgress && (
          <span className="absolute left-3 top-3">
            <Badge tone="degraded">In progress</Badge>
          </span>
        )}
      </div>

      <div className="flex flex-1 flex-col p-5">
        <h3 className="font-heading text-lg font-semibold">
          <Link
            to={`/project/${project.id}`}
            className="after:absolute after:inset-0 after:content-[''] hover:text-teal"
          >
            {project.name}
          </Link>
        </h3>

        <p>
            <small>Small description coming soon</small>
        </p>

        {languages.length > 0 && (
          <ul className="mt-3 flex flex-wrap gap-1.5">
            {languages.map((lang) => (
              <li
                key={lang.name}
                className="inline-flex items-center gap-1.5 rounded-full border border-line px-2 py-0.5 text-xs text-ink-secondary"
              >
                <span
                  className="h-2 w-2 rounded-full"
                  style={{ backgroundColor: lang.color ?? 'var(--color-ink-muted)' }}
                />
                {lang.name}
              </li>
            ))}
          </ul>
        )}

        <div className="mt-auto  flex gap-4 pt-5 text-sm">
          {project.links.repository && (
            <a
              href={project.links.repository}
              target="_blank"
              rel="noreferrer"
              className="relative z-10 inline-flex items-center gap-1.5 text-ink-secondary hover:text-teal"
            >
              <Icon name="fa-brands fa-github" />
              Code
            </a>
          )}
          {project.links.live && (
            <a
              href={project.links.live}
              target="_blank"
              rel="noreferrer"
              className="relative z-10 inline-flex items-center gap-1.5 text-ink-secondary hover:text-teal"
            >
              <Icon name="fa-solid fa-arrow-up-right-from-square" />
              Live
            </a>
          )}
        </div>
      </div>
    </Card>
  )
}
