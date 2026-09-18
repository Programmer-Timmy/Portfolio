import { PageHeading } from '@/components/ui/PageHeading'
import { Container } from '@/components/ui/Container'
import { useDocumentTitle } from '@/lib/useDocumentTitle'

/**
 * Temporary stand-in for pages not yet migrated from the PHP site.
 * Replace each route's element in src/router.tsx with a real page component.
 */
export function PlaceholderPage({ title }: { title: string }) {
  useDocumentTitle(title)

  return (
    <>
      <PageHeading eyebrow="Work in progress" title={title}>
        This page hasn't been migrated to the new frontend yet.
      </PageHeading>
      <Container className="pb-20">
        <div className="rounded-card border border-dashed border-line bg-surface p-8 text-sm text-ink-secondary">
          <p className="font-mono">
            TODO: build <span className="text-teal">{title}</span>. Fetch data via{' '}
            <span className="text-teal">src/lib/api.ts</span>, compose with the UI
            primitives in <span className="text-teal">src/components/ui</span>.
          </p>
        </div>
      </Container>
    </>
  )
}
