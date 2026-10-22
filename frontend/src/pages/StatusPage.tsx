import { Container } from '@/components/ui/Container'
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

const INCIDENT: Record<string, { tone: BadgeTone; label: string }> = {
  danger: STATUS[0],
  warning: STATUS[2],
}

const BANNER: Record<number, { title: string; classes: string; dot: string }> = {
  0: { title: 'There is an outage', classes: 'bg-status-down-bg text-status-down-text', dot: 'bg-status-down' },
  1: { title: 'All systems operational', classes: 'bg-status-up-bg text-status-up-text', dot: 'bg-status-up' },
  2: { title: 'Some services are degraded', classes: 'bg-status-degraded-bg text-status-degraded-text', dot: 'bg-status-degraded' },
  3: { title: 'Some services are unavailable or in maintenance', classes: 'bg-status-maintenance-bg text-status-maintenance-text', dot: 'bg-status-maintenance' },
}

function formatUptime(uptime: number | null) {
  return uptime === null ? '-' : `${(uptime * 100).toFixed(2)}%`
}

export function StatusPage() {
  useDocumentTitle(status.title)

  const { sources, error, loading, updatedAt } = useStatus()
  const pages = sources.flatMap((s) => (s.page ? [s.page] : []))
  const levels = pages.flatMap((p) => p.groups.flatMap((g) => g.monitors.map((m) => m.status)))

  const overall = levels.includes(0)
    ? 0
    : levels.includes(2)
      ? 2
      : sources.some((s) => s.error) || levels.includes(3) || levels.includes(null)
        ? 3
        : 1
  const banner = BANNER[overall]
  const updates = [
    ...pages.flatMap((p) =>
      p.incidents.map((i) => ({
        key: `${p.slug}-i${i.id}`,
        title: i.title,
        body: i.content,
        when: `${i.createdDate.slice(0, 16)} UTC`,
        badge: INCIDENT[i.style] ?? { tone: 'maintenance' as BadgeTone, label: 'Info' },
      })),
    ),
    ...pages.flatMap((p) =>
      p.maintenance.map((m) => ({
        key: `${p.slug}-m${m.id}`,
        title: m.title,
        body: m.description,
        when: '',
        badge: STATUS[3],
      })),
    ),
  ]

  return (
    <Container as="section" className="flex flex-col gap-10 py-12 sm:py-16">
      {loading && <Skeleton className="h-28 w-full rounded-card" />}

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
        <>
          <div className={`flex flex-wrap items-center gap-5 rounded-card px-8 py-7 ${banner.classes}`}>
            <span className={`size-5 shrink-0 rounded-full ${banner.dot}`} />
            <div className="min-w-0">
              <h1 className="text-h2">{banner.title}</h1>
              <p>
                {levels.filter((l) => l === 1).length} of {levels.length} services work normally.
              </p>
            </div>
            {updatedAt && (
              <span className="ml-auto font-mono text-sm">
                {updatedAt.toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' })}
              </span>
            )}
          </div>

          <section className="flex flex-col gap-6">
            <h2 className="text-h2">{status.services}</h2>
            <div className="grid items-start gap-6 [grid-template-columns:repeat(auto-fit,minmax(min(100%,440px),1fr))]">
              {sources.flatMap((source) =>
                source.page ? (
                  source.page.groups.map((group) => (
                    <Card key={`${source.slug}-${group.name}`} className="overflow-hidden">
                      <h3 className="px-6 py-[18px] text-h3">{group.name}</h3>
                      {group.monitors.map((monitor) => {
                        const level = STATUS[monitor.status ?? -1] ?? UNKNOWN
                        return (
                          <div
                            key={monitor.id}
                            className="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-t border-line px-6 py-3.5"
                          >
                            <span className="min-w-0 font-medium">{monitor.name}</span>
                            <span className="flex items-center gap-3">
                              <span className="font-mono text-sm tabular-nums text-ink-secondary">
                                {formatUptime(monitor.uptime)}
                              </span>
                              <Badge tone={level.tone} dot>
                                {level.label}
                              </Badge>
                            </span>
                          </div>
                        )
                      })}
                    </Card>
                  ))
                ) : (
                  <Card key={source.slug}>
                    <CardBody>
                      <h3 className="text-h3">{source.slug}</h3>
                      <p className="mt-1 text-ink-secondary">{status.sourceError}</p>
                      <p className="text-sm text-ink-secondary">{source.error}</p>
                    </CardBody>
                  </Card>
                ),
              )}
            </div>
          </section>

          {updates.length > 0 && (
            <section className="flex flex-col gap-6">
              <h2 className="text-h2">{status.updates}</h2>
              {updates.map((update) => (
                <Card key={update.key}>
                  <CardBody className="flex flex-col gap-2">
                    <div className="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                      <h3 className="text-h3">{update.title}</h3>
                      <Badge tone={update.badge.tone} dot>
                        {update.badge.label}
                      </Badge>
                    </div>
                    <p className="whitespace-pre-line">{update.body}</p>
                    {update.when && (
                      <span className="font-mono text-sm text-ink-secondary">{update.when}</span>
                    )}
                  </CardBody>
                </Card>
              ))}
            </section>
          )}
        </>
      )}
    </Container>
  )
}
