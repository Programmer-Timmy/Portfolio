import { Container } from '@/components/ui/Container'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'

/**
 * Minimal home page — enough to show the design system in place.
 * Flesh this out as the first real migration target.
 */
export function HomePage() {
  return (
    <Container as="section" className="py-20 sm:py-28">
      <Badge>Web development &amp; technology</Badge>
      <h1 className="mt-5 max-w-3xl text-h1">
        I build practical software — for clients, in the open, and for Scouting.
      </h1>
      <p className="mt-5 max-w-xl text-lg text-ink-secondary">
        Hi, I'm Tim van der Kloet. This is the new home of my portfolio, being
        rebuilt with React and Tailwind on top of the existing backend.
      </p>
      <div className="mt-8 flex flex-wrap gap-3">
        <Button to="/projects">View projects</Button>
        <Button to="/contact" variant="secondary">
          Get in touch
        </Button>
      </div>
    </Container>
  )
}
