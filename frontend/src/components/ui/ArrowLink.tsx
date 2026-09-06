import type { ReactNode } from 'react'
import { Link } from 'react-router-dom'
import { cn } from '@/lib/cn'
import { isMigrated } from '@/lib/migrated'

/**
 * A "see more" / "go back" link: a label that underlines on hover next to an
 * arrow that nudges in the direction of travel. Same element-picking rules as
 * TextLink (absolute URL opens a new tab, migrated path is a client route,
 * else a full nav).
 */
export function ArrowLink({
  to,
  children,
  direction = 'forward',
  className,
}: {
  to: string
  children: ReactNode
  direction?: 'forward' | 'back'
  className?: string
}) {
  const classes = cn(
    'group inline-flex items-center gap-1 text-sm font-medium text-teal',
    className,
  )

  const label = (
    <span className="underline decoration-transparent underline-offset-2 transition-[text-decoration-color] group-hover:decoration-current">
      {children}
    </span>
  )
  const arrow = (
    <span
      aria-hidden="true"
      className={cn(
        'transition-transform',
        direction === 'back'
          ? 'group-hover:-translate-x-0.5'
          : 'group-hover:translate-x-0.5',
      )}
    >
      {direction === 'back' ? '←' : '→'}
    </span>
  )

  const inner =
    direction === 'back' ? (
      <>
        {arrow}
        {label}
      </>
    ) : (
      <>
        {label}
        {arrow}
      </>
    )

  if (/^(https?:)?\/\//i.test(to)) {
    return (
      <a href={to} target="_blank" rel="noopener noreferrer" className={classes}>
        {inner}
      </a>
    )
  }

  if (isMigrated(to)) {
    return (
      <Link to={to} className={classes}>
        {inner}
      </Link>
    )
  }

  return (
    <a href={to} className={classes}>
      {inner}
    </a>
  )
}
