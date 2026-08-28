import { useEffect, useState } from 'react'
import { Link, NavLink, useLocation } from 'react-router-dom'
import { cn } from '@/lib/cn'
import { useTheme } from '@/lib/theme'
import { Logo } from '@/components/ui/Logo'
import { Container } from '@/components/ui/Container'
import { NAV_ITEMS } from './nav-items'
import { ThemeToggle } from './ThemeToggle'

export function SiteHeader() {
  const [open, setOpen] = useState(false)
  const { theme, toggle } = useTheme()
  const location = useLocation()

  // Close the mobile menu on route change.
  useEffect(() => setOpen(false), [location.pathname])

  const linkClass = ({ isActive }: { isActive: boolean }) =>
    cn(
      'rounded-md px-3 py-2 text-sm font-medium transition-colors',
      isActive
        ? 'text-teal'
        : 'text-ink-secondary hover:text-ink',
    )

  return (
    <header className="sticky top-0 z-40 border-b border-line bg-surface/85 backdrop-blur">
      <Container className="flex h-16 items-center justify-between gap-4">
        <Link to="/" aria-label="Tim van der Kloet — home" className="shrink-0">
          <Logo className="h-8" />
        </Link>

        <nav aria-label="Main" className="hidden items-center gap-1 md:flex">
          {NAV_ITEMS.map((item) => (
            <NavLink key={item.to} to={item.to} end={item.to === '/'} className={linkClass}>
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="flex items-center gap-2">
          <ThemeToggle theme={theme} onToggle={toggle} />
          <button
            type="button"
            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-line text-ink-secondary md:hidden"
            aria-expanded={open}
            aria-controls="mobile-nav"
            aria-label="Toggle navigation"
            onClick={() => setOpen((v) => !v)}
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              {open ? (
                <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
              ) : (
                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
              )}
            </svg>
          </button>
        </div>
      </Container>

      {open && (
        <nav
          id="mobile-nav"
          aria-label="Mobile"
          className="border-t border-line bg-surface md:hidden"
        >
          <Container className="flex flex-col py-2">
            {NAV_ITEMS.map((item) => (
              <NavLink
                key={item.to}
                to={item.to}
                end={item.to === '/'}
                className={({ isActive }) =>
                  cn(
                    'rounded-md px-3 py-3 text-sm font-medium',
                    isActive ? 'text-teal' : 'text-ink-secondary',
                  )
                }
              >
                {item.label}
              </NavLink>
            ))}
          </Container>
        </nav>
      )}
    </header>
  )
}
