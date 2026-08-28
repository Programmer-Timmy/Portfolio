import { cn } from '@/lib/cn'

/**
 * Font Awesome icon wrapper. `name` is a full FA class string, e.g.
 * "fa-brands fa-github" or "fa-solid fa-envelope". Decorative by default;
 * pass a `title` when the icon carries meaning on its own.
 */
export function Icon({
  name,
  className,
  title,
}: {
  name: string
  className?: string
  title?: string
}) {
  return (
    <i
      className={cn(name, className)}
      aria-hidden={title ? undefined : true}
      aria-label={title}
      role={title ? 'img' : undefined}
    />
  )
}
