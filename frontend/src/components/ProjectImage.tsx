import { useState } from 'react'
import type { ApiImage } from '@/lib/types'
import { cn } from '@/lib/cn'

/**
 * Project image with a graceful fallback. Many images live only on the
 * production server, so a broken `src` falls back to a branded placeholder.
 */
export function ProjectImage({
  image,
  alt,
  className,
}: {
  image: ApiImage | null
  alt: string
  className?: string
}) {
  const [failed, setFailed] = useState(false)

  if (!image || failed) {
    return (
      <div
        className={cn(
          'flex items-center justify-center bg-navy/5 text-navy/30 dark:bg-white/5 dark:text-white/25',
          className,
        )}
        aria-hidden="true"
      >
        <span className="font-heading text-3xl font-extrabold tracking-tight">
          &lt;TK/&gt;
        </span>
      </div>
    )
  }

  return (
    <picture>
      {image.webp && <source srcSet={image.webp} type="image/webp" />}
      <img
        src={image.src}
        srcSet={image.srcset}
        alt={alt}
        loading="lazy"
        decoding="async"
        onError={() => setFailed(true)}
        className={cn('object-cover', className)}
      />
    </picture>
  )
}
