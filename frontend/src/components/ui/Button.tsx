import type { ButtonHTMLAttributes, AnchorHTMLAttributes, ReactNode } from 'react'
import { cn } from '@/lib/cn'
import { isMigrated } from '@/lib/migrated'
import { Link } from 'react-router-dom'

type Variant = 'primary' | 'secondary' | 'outline' | 'ghost' | 'destructive'
type Size = 'sm' | 'md' | 'lg'

// Only `transform` transitions - the fill/border swap on hover is instant (a
// deliberate choice: snappier, and it sidesteps a browser bug where a colour
// mid-transition sticks when the light/dark palette swaps underneath it).
const base =
  'inline-flex items-center justify-center gap-2 rounded-lg font-medium ' +
  'transition-transform active:scale-[0.98] ' +
  'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal ' +
  'disabled:cursor-not-allowed disabled:opacity-55 disabled:active:scale-100'

// Filled variants darken on hover / press (never lighten the accent). outline and
// ghost use a neutral ink wash - `--color-ink` flips per theme so it reads right
// on both. Reserve `primary` for the one main action per view.
//  primary     → solid teal
//  secondary   → solid navy (raised slate in dark)
//  outline     → 1px border, ink text
//  ghost       → text only
//  destructive → solid red (admin / delete flows)
const variants: Record<Variant, string> = {
  primary: 'bg-teal text-white hover:bg-teal-hover active:bg-teal-active',
  secondary: 'bg-btn2 text-btn2-fg hover:bg-btn2-hover',
  outline:
    'border border-line text-ink hover:border-ink-muted hover:bg-ink/[0.045]',
  ghost: 'text-ink-secondary hover:bg-ink/[0.055] hover:text-ink',
  destructive:
    'bg-danger text-white hover:bg-danger-hover active:bg-danger-active',
}

const sizes: Record<Size, string> = {
  sm: 'h-9 px-3 text-sm',
  md: 'h-11 px-5 text-sm',
  lg: 'h-12 px-6 text-base',
}

type CommonProps = {
  variant?: Variant
  size?: Size
  className?: string
  children: ReactNode
}

type ButtonAsButton = CommonProps &
  Omit<ButtonHTMLAttributes<HTMLButtonElement>, keyof CommonProps> & {
    to?: undefined
    href?: undefined
  }

type ButtonAsLink = CommonProps & { to: string } & Omit<
    AnchorHTMLAttributes<HTMLAnchorElement>,
    keyof CommonProps | 'href'
  >

type ButtonAsAnchor = CommonProps & { href: string } & Omit<
    AnchorHTMLAttributes<HTMLAnchorElement>,
    keyof CommonProps
  >

export function Button(props: ButtonAsButton | ButtonAsLink | ButtonAsAnchor) {
  const { variant = 'primary', size = 'md', className, children } = props
  const classes = cn(base, variants[variant], sizes[size], className)

  if ('to' in props && props.to !== undefined) {
    const { variant: _v, size: _s, className: _c, children: _ch, to, ...rest } = props
    // Client-side transition only for routes React owns; otherwise a full
    // navigation so PHP renders the (not-yet-migrated) page.
    if (isMigrated(to)) {
      return (
        <Link to={to} className={classes} {...rest}>
          {children}
        </Link>
      )
    }
    return (
      <a href={to} className={classes} {...rest}>
        {children}
      </a>
    )
  }

  if ('href' in props && props.href !== undefined) {
    const {
      variant: _v,
      size: _s,
      className: _c,
      children: _ch,
      href,
      target,
      rel,
      ...rest
    } = props
    // Absolute URLs leave the site, so open them in a new tab unless the caller
    // said otherwise. mailto:/tel: and in-app paths are left alone.
    const external = /^(https?:)?\/\//i.test(href)
    return (
      <a
        href={href}
        className={classes}
        target={target ?? (external ? '_blank' : undefined)}
        rel={rel ?? (external ? 'noopener noreferrer' : undefined)}
        {...rest}
      >
        {children}
      </a>
    )
  }

  const { variant: _v, size: _s, className: _c, children: _ch, ...rest } =
    props as ButtonAsButton
  return (
    <button className={classes} {...rest}>
      {children}
    </button>
  )
}
