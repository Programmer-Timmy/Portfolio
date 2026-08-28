import type { HTMLAttributes, ReactNode } from 'react'
import { cn } from '@/lib/cn'

type CardProps = HTMLAttributes<HTMLDivElement> & {
  children: ReactNode
  /** Adds hover lift + teal border — use for cards that link somewhere. */
  interactive?: boolean
}

/** White surface on paper background, 1px line border, soft shadow. */
export function Card({ children, className, interactive, ...rest }: CardProps) {
  return (
    <div
      className={cn(
        'rounded-card border border-line bg-surface shadow-card',
        interactive &&
          'transition-colors transition-shadow hover:border-teal/40 hover:shadow-md',
        className,
      )}
      {...rest}
    >
      {children}
    </div>
  )
}

export function CardBody({ children, className }: { children: ReactNode; className?: string }) {
  return <div className={cn('p-5 sm:p-6', className)}>{children}</div>
}
