import type { ReactNode } from 'react'
import { cn } from '@/lib/cn'
import { Container } from './Container'
import { AppLink } from './AppLink'

type SectionProps = {
  id?: string
  eyebrow?: string
  title?: string
  description?: ReactNode
  /** Optional "see all" style link shown next to the title. */
  action?: { label: string; to: string }
  children: ReactNode
  className?: string
  /** Contrasting band (white in light, raised grey in dark) with hairline rules. */
  muted?: boolean
}

export function Section({
  id,
  eyebrow,
  title,
  description,
  action,
  children,
  className,
  muted,
}: SectionProps) {
  return (
    <section
      id={id}
      className={cn(
        'py-16 sm:py-20',
        muted && 'border-y border-line bg-surface',
        className,
      )}
    >
      <Container>
        {(eyebrow || title || action) && (
          <div className="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div className="max-w-2xl">
              {eyebrow && (
                <p className="mb-2 font-mono text-xs uppercase tracking-widest text-teal">
                  {eyebrow}
                </p>
              )}
              {title && <h2 className="text-h2">{title}</h2>}
              {description && (
                <div className="mt-3 text-ink-secondary">{description}</div>
              )}
            </div>
            {action && (
              <AppLink
                to={action.to}
                className="text-sm font-medium text-teal hover:underline"
              >
                {action.label} →
              </AppLink>
            )}
          </div>
        )}
        {children}
      </Container>
    </section>
  )
}
