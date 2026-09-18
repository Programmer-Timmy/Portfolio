import type { AnchorHTMLAttributes, ReactNode } from 'react'
import { NavLink } from 'react-router-dom'
import { isMigrated } from '@/lib/migrated'

type AppLinkProps = {
  to: string
  children: ReactNode
  className?: string | ((state: { isActive: boolean }) => string)
  /** Match the whole path for active state (react-router `end`). */
  end?: boolean
} & Omit<AnchorHTMLAttributes<HTMLAnchorElement>, 'href' | 'className'>

/**
 * Links to a migrated route with a client-side <NavLink>; links to a route
 * still served by PHP with a plain <a> so the browser does a full navigation.
 */
export function AppLink({ to, children, className, end, ...rest }: AppLinkProps) {
  if (isMigrated(to)) {
    return (
      <NavLink to={to} end={end} className={className} {...rest}>
        {children}
      </NavLink>
    )
  }

  const flatClassName =
    typeof className === 'function' ? className({ isActive: false }) : className

  return (
    <a href={to} className={flatClassName} {...rest}>
      {children}
    </a>
  )
}
