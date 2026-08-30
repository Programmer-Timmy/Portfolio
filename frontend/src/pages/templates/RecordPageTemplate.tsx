/* -------------------------------------------------------------------------- *
 *  TEMPLATE: a single-record / content page
 *  (About, Contact, a project at /projects/:id, an open-source item, ...)
 *
 *  Copy this file to src/pages/<Name>Page.tsx, then:
 *   1. rename the component, set the title / copy / endpoint
 *   2. replace `RecordData` with the real type from src/lib/types.ts
 *   3. fill the main column and the aside
 *   4. for a static page (About / Contact) drop `useParams` and point
 *      `useApi` at a fixed path like '/profile' (or remove it entirely)
 *   5. add the route in src/router.tsx  (e.g. path: 'projects/:id')
 *   6. add the path to MIGRATED_ROUTES (src/lib/migrated.ts) and to
 *      $site['spa']['routes'] in private/config/settings.php
 * -------------------------------------------------------------------------- */

import { useParams } from 'react-router-dom'
import { Container } from '@/components/ui/Container'
import { Card } from '@/components/ui/Card'
import { Skeleton } from '@/components/ui/Skeleton'
import { Button } from '@/components/ui/Button'
import { AppLink } from '@/components/ui/AppLink'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'

// Replace with e.g. `import type { ProjectDetail } from '@/lib/types'`
type RecordData = { id: number; name: string; body: string }

export function RecordPageTemplate() {
  const { id } = useParams<{ id: string }>()

  // For a static page (About / Contact) swap this for a fixed path,
  // e.g. useApi<Profile>('/profile').
  const query = useApi<RecordData>(`/records/${id}`)

  useDocumentTitle(query.status === 'success' ? query.data.name : 'Loading')

  return (
    <Container as="article" className="py-14 sm:py-20">
      <AppLink to="/collection" className="text-sm font-medium text-teal hover:underline">
        ← Back
      </AppLink>

      {query.status === 'loading' && (
        <div className="mt-6 space-y-4">
          <Skeleton className="h-10 w-2/3" />
          <Skeleton className="h-5 w-full" />
          <Skeleton className="h-5 w-5/6" />
        </div>
      )}

      {query.status === 'error' && (
        <div className="mt-10 rounded-card border border-line bg-surface p-8 text-center">
          <p className="text-ink-secondary">
            {query.error.status === 404
              ? "That page doesn't exist."
              : query.error.message}
          </p>
          <div className="mt-4 flex justify-center gap-3">
            {query.error.status !== 404 && (
              <Button size="sm" variant="secondary" onClick={query.reload}>
                Try again
              </Button>
            )}
            <Button to="/" size="sm">
              Home
            </Button>
          </div>
        </div>
      )}

      {query.status === 'success' && (
        <div className="mt-6 grid gap-10 lg:grid-cols-[1.6fr_1fr] lg:items-start">
          <div>
            <h1 className="text-h1">{query.data.name}</h1>
            <div className="mt-6 space-y-4 text-ink-secondary">
              <p>{query.data.body}</p>
            </div>
          </div>

          <Card className="p-6">
            <p className="font-mono text-xs uppercase tracking-widest text-teal">
              Details
            </p>
            {/* metadata, links, gallery thumbnails, contact info, ... */}
          </Card>
        </div>
      )}
    </Container>
  )
}
