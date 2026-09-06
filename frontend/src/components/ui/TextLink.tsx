import type { AnchorHTMLAttributes, ReactNode } from 'react'
import { Link } from 'react-router-dom'
import { cn } from '@/lib/cn'
import { isMigrated } from '@/lib/migrated'

type TextLinkProps = {
  /** A path ('/contact'), an absolute URL, or a mailto:/tel: link. */
  to: string
  children: ReactNode
  className?: string
} & Omit<AnchorHTMLAttributes<HTMLAnchorElement>, 'href' | 'className'>

/**
 * An inline link for use inside a paragraph of text. Teal, always underlined but
 * faintly, so it reads as a link at rest; the underline firms up on hover.
 * Picks the right element automatically:
 *  - absolute URL  -> <a> opening in a new tab
 *  - mailto:/tel:  -> plain <a>
 *  - migrated path -> client-side <Link>
 *  - other path    -> <a> so PHP renders the not-yet-migrated page
 *
 * For standalone calls-to-action use <Button>; for nav links use <AppLink>.
 */
export function TextLink({ to, children, className, ...rest }: TextLinkProps) {
  const classes = cn(
    'font-medium text-teal underline decoration-teal/35 underline-offset-2',
    'transition-[text-decoration-color] hover:decoration-teal',
    'focus-visible:rounded-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal',
    className,
  )

  if (/^(https?:)?\/\//i.test(to)) {
    return (
      <a
        href={to}
        className={classes}
        target="_blank"
        rel="noopener noreferrer"
        {...rest}
      >
        {children}
      </a>
    )
  }

  if (/^(mailto:|tel:)/i.test(to)) {
    return (
      <a href={to} className={classes} {...rest}>
        {children}
      </a>
    )
  }

  if (isMigrated(to)) {
    return (
      <Link to={to} className={classes} {...rest}>
        {children}
      </Link>
    )
  }

  return (
    <a href={to} className={classes} {...rest}>
      {children}
    </a>
  )
}
