import { cn } from '@/lib/cn'

/**
 * Loading placeholder with a sheen that sweeps left -> right.
 * The `shimmer` keyframes + `--animate-shimmer` token live in styles/index.css.
 * The keyframes own the full transform, so no translate utility here (that
 * would fight the animation via the separate `translate` property in v4).
 */
export function Skeleton({ className }: { className?: string }) {
  return (
    <div
      className={cn(
        'relative isolate overflow-hidden rounded-md bg-line/70',
        "before:absolute before:inset-0 before:content-['']",
        'before:animate-shimmer',
        'before:bg-gradient-to-r before:from-transparent before:via-white/45 before:to-transparent',
        'dark:before:via-white/10',
        'motion-reduce:before:hidden',
        className,
      )}
      aria-hidden="true"
    />
  )
}
