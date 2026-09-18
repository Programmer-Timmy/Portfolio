import type { ReactNode } from 'react'
import { Container } from './Container'

/** Standard page title block. */
export function PageHeading({
  eyebrow,
  title,
  children,
}: {
  eyebrow?: string
  title: string
  children?: ReactNode
}) {
  return (
    <Container as="section" className="py-14 sm:py-20">
      {eyebrow && (
        <p className="mb-3 font-mono text-xs uppercase tracking-widest text-teal">
          {eyebrow}
        </p>
      )}
      <h1 className="text-h1">{title}</h1>
      {children && (
        <div className="mt-4 max-w-2xl text-lg text-ink-secondary">{children}</div>
      )}
    </Container>
  )
}
