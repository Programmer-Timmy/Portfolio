import { Container } from '@/components/ui/Container'
import { PageHeading } from '@/components/ui/PageHeading'
import { Card, CardBody } from '@/components/ui/Card'
import { Badge, type BadgeTone } from '@/components/ui/Badge'
import { Skeleton } from '@/components/ui/Skeleton'
import { useStatus } from '@/lib/useStatus'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import { status } from '@/content/status'

const STATUS: Record<number, { tone: BadgeTone; label: string }> = {
  0: { tone: 'down', label: 'Outage' },
  1: { tone: 'up', label: 'Operational' },
  2: { tone: 'degraded', label: 'Degraded' },
  3: { tone: 'maintenance', label: 'Maintenance' },
}
const UNKNOWN = { tone: 'maintenance' as BadgeTone, label: 'Unknown' }

const BANNER: Record<number, string> = {
  0: 'Some systems are down',
  1: 'All systems operational',
  2: 'Some systems are degraded',
  3: 'Status partly unavailable or in maintenance',
}

function formatUptime(uptime: number | null) {
  return uptime === null ? '-' : `${(uptime * 100).toFixed(2)}%`
}

export function StatusPage() {
  useDocumentTitle(status.title)

  const { sources, error, loading } = useStatus()
  const pages = sources.flatMap((s) => (s.page ? [s.page] : []))
  const levels = pages.flatMap((p) => p.groups.flatMap((g) => g.monitors.map((m) => m.status)))

  const overall = levels.includes(0)
    ? 0
    : levels.includes(2)
      ? 2
      : sources.some((s) => s.error) || levels.includes(3) || levels.includes(null)
        ? 3
        : 1

  return (
    <>
      <PageHeading title={status.title}>{status.lead}</PageHeading>

      <Container as="section" className="pb-24">
        {loading && <Skeleton className="h-40 w-full rounded-card" />}

        {error && (
          <Card>
            <CardBody>
              <p className="font-medium text-ink">{status.errorTitle}</p>
              <p className="mt-1 text-sm text-ink-secondary">{error}</p>
            </CardBody>
          </Card>
        )}

        {!loading && !error && sources.length === 0 && (
          <p className="text-ink-secondary">{status.empty}</p>
        )}

        {sources.length > 0 && (
          <div className="space-y-8">
            <Card>
              <CardBody className="flex items-center justify-between gap-4">
                <h2 className="text-h3">{BANNER[overall]}</h2>
                <Badge tone={STATUS[overall].tone}>{STATUS[overall].label}</Badge>
              </CardBody>
            </Card>

            {pages.some((p) => p.incidents.length > 0) && (
              <section>
                <h2 className="mb-3 text-h3">{status.incidents}</h2>
                <ul className="space-y-3">
                  {pages.flatMap((p) =>
                    p.incidents.map((incident) => (
                      <li key={`${p.slug}-${incident.id}`}>
                        <Card>
                          <CardBody>
                            <p className="font-medium text-ink">{incident.title}</p>
                            <p className="mt-1 font-mono text-xs text-ink-muted">
                              {incident.createdDate.slice(0, 16)} UTC
                            </p>
                            <p className="mt-2 whitespace-pre-line text-ink-secondary">{incident.content}</p>
                          </CardBody>
                        </Card>
                      </li>
                    )),
                  )}
                </ul>
              </section>
            )}

            {pages.some((p) => p.maintenance.length > 0) && (
              <section>
                <h2 className="mb-3 text-h3">{status.maintenance}</h2>
                <ul className="space-y-3">
                  {pages.flatMap((p) =>
                    p.maintenance.map((item) => (
                      <li key={`${p.slug}-${item.id}`}>
                        <Card>
                          <CardBody>
                            <p className="font-medium text-ink">{item.title}</p>
                            <p className="mt-2 whitespace-pre-line text-ink-secondary">{item.description}</p>
                          </CardBody>
                        </Card>
                      </li>
                    )),
                  )}
                </ul>
              </section>
            )}

            {sources.map((source) => (
              <section key={source.slug}>
                <h2 className="mb-3 text-h3">{source.page?.title ?? source.slug}</h2>
                {source.page ? (
                  <div className="space-y-4">
                    {source.page.groups.map((group) => (
                      <Card key={group.name}>
                        <CardBody>
                          <h3 className="mb-3 text-base font-semibold">{group.name}</h3>
                          <ul className="divide-y divide-line">
                            {group.monitors.map((monitor) => {
                              const level = STATUS[monitor.status ?? -1] ?? UNKNOWN
                              return (
                                <li key={monitor.id} className="flex items-center justify-between gap-4 py-3">
                                  <span className="text-ink">{monitor.name}</span>
                                  <span className="flex items-center gap-3">
                                    <span className="font-mono text-sm text-ink-secondary">
                                      {formatUptime(monitor.uptime)}
                                    </span>
                                    <Badge tone={level.tone}>{level.label}</Badge>
                                  </span>
                                </li>
                              )
                            })}
                          </ul>
                        </CardBody>
                      </Card>
                    ))}
                  </div>
                ) : (
                  <Card>
                    <CardBody>
                      <p className="font-medium text-ink">{status.sourceError}</p>
                      <p className="mt-1 text-sm text-ink-secondary">{source.error}</p>
                    </CardBody>
                  </Card>
                )}
              </section>
            ))}
          </div>
        )}
      </Container>
    </>
  )
}
