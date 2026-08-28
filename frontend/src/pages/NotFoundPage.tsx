import { Container } from '@/components/ui/Container'
import { Button } from '@/components/ui/Button'

export function NotFoundPage() {
  return (
    <Container as="section" className="py-28 text-center">
      <p className="font-mono text-sm uppercase tracking-widest text-teal">
        Error 404
      </p>
      <h1 className="mt-4 text-h1">This page could not be found.</h1>
      <p className="mx-auto mt-4 max-w-md text-ink-secondary">
        The link may be broken, or the page may have moved.
      </p>
      <div className="mt-8 flex justify-center">
        <Button to="/">Back to home</Button>
      </div>
    </Container>
  )
}
