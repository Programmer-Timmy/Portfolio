import type { ElementType, ReactNode } from 'react'
import { cn } from '@/lib/cn'

type ContainerProps = {
  as?: ElementType
  children: ReactNode
  className?: string
}

/** Centered content column with the site's standard gutters and max width. */
export function Container({ as: Tag = 'div', children, className }: ContainerProps) {
  return (
    <Tag className={cn('mx-auto w-full max-w-6xl px-5 sm:px-6 lg:px-8', className)}>
      {children}
    </Tag>
  )
}
