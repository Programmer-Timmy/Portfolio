import { Link } from 'react-router-dom'
import { Container } from '@/components/ui/Container'
import { Logo } from '@/components/ui/Logo'
import { NAV_ITEMS } from './nav-items'

const SOCIAL = [
  { label: 'GitHub', href: 'https://github.com/Programmer-Timmy' },
  { label: 'CV', href: '/doc/CV.pdf' },
]

export function SiteFooter() {
  return (
    // Styleguide §5: Ink Navy background, light text, teal links.
    <footer className="mt-24 bg-navy text-white/80">
      <Container className="grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr]">
        <div>
          <Logo tone="onDark" />
          <p className="mt-4 max-w-sm text-sm leading-relaxed text-white/70">
            Web development &amp; technology. I build practical software — for
            clients, in the open, and for Scouting.
          </p>
        </div>

        <nav aria-label="Footer">
          <h2 className="font-heading text-sm font-semibold tracking-wide text-white">
            Pages
          </h2>
          <ul className="mt-4 space-y-2 text-sm">
            {NAV_ITEMS.map((item) => (
              <li key={item.to}>
                <Link to={item.to} className="text-white/70 transition-colors hover:text-teal-light">
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div>
          <h2 className="font-heading text-sm font-semibold tracking-wide text-white">
            Elsewhere
          </h2>
          <ul className="mt-4 space-y-2 text-sm">
            {SOCIAL.map((item) => (
              <li key={item.label}>
                <a
                  href={item.href}
                  className="text-white/70 transition-colors hover:text-teal-light"
                  target="_blank"
                  rel="noreferrer"
                >
                  {item.label}
                </a>
              </li>
            ))}
          </ul>
        </div>
      </Container>

      <div className="border-t border-white/10">
        <Container className="flex flex-col gap-2 py-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
          <p>© {new Date().getFullYear()} Tim van der Kloet. All rights reserved.</p>
          <p className="font-mono">Built with React &amp; Tailwind</p>
        </Container>
      </div>
    </footer>
  )
}
