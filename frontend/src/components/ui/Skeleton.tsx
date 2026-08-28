import { cn } from '@/lib/cn'

/**
 * Loading placeholder with a sheen that sweeps left -> right.
 * The `shimmer` keyframes + `--animate-shimmer` token live in styles/index.css.
 */
export function Skeleton({ className }: { className?: string }) {
  return (
    <div
      className={cn(
        'relative isolate overflow-hidden rounded-md bg-line/70',
        'before:absolute before:inset-0 before:-translate-x-full',
        'before:animate-shimmer before:bg-gradient-to-r',
        'before:from-transparent before:via-white/25 before:to-transparent',
        'motion-reduce:before:hidden',
        className,
      )}
      aria-hidden="true"
    />
  )
}
