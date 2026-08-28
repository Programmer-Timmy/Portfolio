import { cn } from '@/lib/cn'

type LogoProps = {
  /** `icon` = the <TK/> mark only, `wordmark` = mark + name. */
  variant?: 'icon' | 'wordmark'
  /**
   * `auto`   navy/teal art, flips to white in dark mode (default, for headers)
   * `light`  always the navy/teal art (use on white/paper surfaces)
   * `onDark` always white (use on the navy footer / dark hero)
   */
  tone?: 'auto' | 'light' | 'onDark'
  className?: string
}

const toneClass: Record<NonNullable<LogoProps['tone']>, string> = {
  auto: 'dark:brightness-0 dark:invert',
  light: '',
  onDark: 'brightness-0 invert',
}

/**
 * Brand logo. Assets live in /public/brand (English wordmark + universal
 * <TK/> icon). Never recolour outside navy/teal; keep clear space ≈ the
 * height of the "T". The dark-mode treatment is a monochrome white flip
 * until a dedicated light logo asset exists.
 */
export function Logo({ variant = 'wordmark', tone = 'auto', className }: LogoProps) {
  const src = variant === 'icon' ? '/brand/icon.svg' : '/brand/wordmark-full.png'
  const alt =
    variant === 'icon'
      ? 'Tim van der Kloet'
      : 'Tim van der Kloet, Web Development and Technology'

  return (
    <img
      src={src}
      alt={alt}
      className={cn('block w-auto', variant === 'icon' ? 'h-8' : 'h-9', toneClass[tone], className)}
    />
  )
}
