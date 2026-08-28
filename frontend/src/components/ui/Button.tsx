import type { ButtonHTMLAttributes, AnchorHTMLAttributes, ReactNode } from 'react'
import { Link } from 'react-router-dom'
import { cn } from '@/lib/cn'

type Variant = 'primary' | 'secondary' | 'ghost'
type Size = 'sm' | 'md' | 'lg'

const base =
  'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-colors ' +
  'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal ' +
  'disabled:cursor-not-allowed disabled:opacity-55'

// Styleguide §5:
//  primary   → teal bg, white text, hover teal-light
//  secondary → transparent w/ navy border + navy text, hover light teal tint
const variants: Record<Variant, string> = {
  primary: 'bg-teal text-white hover:bg-teal-light',
  secondary:
    'border border-navy text-navy hover:bg-teal/10 dark:border-ink dark:text-ink',
  ghost: 'text-teal hover:bg-teal/10',
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
    const { variant: _v, size: _s, className: _c, children: _ch, ...rest } = props
    return (
      <Link className={classes} {...rest}>
        {children}
      </Link>
    )
  }

  if ('href' in props && props.href !== undefined) {
    const { variant: _v, size: _s, className: _c, children: _ch, ...rest } = props
    return (
      <a className={classes} {...rest}>
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
