import type { ReactNode } from 'react'
import { cn } from '@/lib/cn'

type BadgeTone = 'brand' | 'up' | 'degraded' | 'down' | 'maintenance'

// Non-status badges use a teal tint (styleguide §5). Status tones map to the
// semantic contrast variants (§2.3) — keep these OFF marketing content.
const tones: Record<BadgeTone, string> = {
  brand: 'bg-teal/12 text-teal',
  up: 'bg-status-up-bg text-status-up-text',
  degraded: 'bg-status-degraded-bg text-status-degraded-text',
  down: 'bg-status-down-bg text-status-down-text',
  maintenance: 'bg-status-maintenance-bg text-status-maintenance-text',
}

export function Badge({
  children,
  tone = 'brand',
  className,
}: {
  children: ReactNode
  tone?: BadgeTone
  className?: string
}) {
  return (
    <span
      className={cn(
        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
        tones[tone],
        className,
      )}
    >
      {children}
    </span>
  )
}
