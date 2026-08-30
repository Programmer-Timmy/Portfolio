/* -------------------------------------------------------------------------- *
 *  TEMPLATE: a list / index page (Projects, Open Source, Videos)
 *
 *  Copy this file to src/pages/<Name>Page.tsx, then:
 *   1. rename the component, set the title / copy / endpoint
 *   2. replace `Item` with the real type from src/lib/types.ts
 *   3. build the real card in <ItemCard>
 *   4. add the route in src/router.tsx
 *   5. add the path to MIGRATED_ROUTES (src/lib/migrated.ts)
 *      and to $site['spa']['routes'] in private/config/settings.php
 * -------------------------------------------------------------------------- */

import { Container } from '@/components/ui/Container'
import { PageHeading } from '@/components/ui/PageHeading'
import { Card } from '@/components/ui/Card'
import { Skeleton } from '@/components/ui/Skeleton'
import { Button } from '@/components/ui/Button'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'

// Replace with e.g. `import type { ProjectSummary } from '@/lib/types'`
type Item = { id: number; name: string }

export function CollectionPageTemplate() {
  useDocumentTitle('Collection')

  // e.g. useApi<ProjectSummary[]>('/projects')
  const query = useApi<Item[]>('/collection')

  return (
    <>
      <PageHeading eyebrow="Section label" title="Collection">
        One or two sentences on what this page is. Keep it short; the items
        carry the weight.
      </PageHeading>

      <Container as="section" className="pb-24">
        {/* Optional: a link out to the source of truth (GitHub, YouTube, ...) */}
        <div className="mb-8">
          <Button href="https://github.com/Programmer-Timmy" variant="secondary" size="sm">
            Related external link
          </Button>
        </div>

        {query.status === 'loading' && (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }, (_, i) => (
              <Skeleton key={i} className="h-64 w-full rounded-card" />
            ))}
          </div>
        )}

        {query.status === 'error' && (
          <div className="rounded-card border border-line bg-surface p-8 text-center">
            <p className="text-ink-secondary">{query.error.message}</p>
            <div className="mt-4 flex justify-center">
              <Button size="sm" variant="secondary" onClick={query.reload}>
                Try again
              </Button>
            </div>
          </div>
        )}

        {query.status === 'success' && query.data.length === 0 && (
          <p className="text-ink-secondary">Nothing here yet.</p>
        )}

        {query.status === 'success' && query.data.length > 0 && (
          <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {query.data.map((item) => (
              <li key={item.id}>
                <ItemCard item={item} />
              </li>
            ))}
          </ul>
        )}
      </Container>
    </>
  )
}

function ItemCard({ item }: { item: Item }) {
  return (
    <Card interactive className="h-full p-5">
      <h2 className="font-heading text-lg font-semibold">{item.name}</h2>
      {/* image, tags, links, ... */}
    </Card>
  )
}
