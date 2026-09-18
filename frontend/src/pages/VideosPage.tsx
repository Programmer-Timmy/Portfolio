import { useState } from 'react'
import { Container } from '@/components/ui/Container'
import { PageHeading } from '@/components/ui/PageHeading'
import { Button } from '@/components/ui/Button'
import { ArrowLink } from '@/components/ui/ArrowLink'
import { Skeleton } from '@/components/ui/Skeleton'
import { Icon } from '@/components/ui/Icon'
import { useApi } from '@/lib/useApi'
import { useDocumentTitle } from '@/lib/useDocumentTitle'
import type { Video } from '@/lib/types'
import { videos } from '@/content/videos'

export function VideosPage() {
  useDocumentTitle('Videos')

  const query = useApi<Video[]>('/videos')

  return (
    <>
      <PageHeading title={videos.title}>
        {videos.lead}
      </PageHeading>

      <Container as="section" className="pb-24">
        <div className="mb-8">
          <Button href={videos.source.url} variant="outline" size="sm">
            <Icon name="fa-brands fa-youtube" />
            {videos.source.label}
          </Button>
        </div>

        {query.status === 'loading' && (
          <div className="grid gap-8 sm:grid-cols-2">
            {Array.from({ length: 4 }, (_, i) => (
              <Skeleton key={i} className="aspect-video w-full rounded-card" />
            ))}
          </div>
        )}

        {query.status === 'error' && (
          <div className="rounded-card border border-line bg-surface p-8 text-center">
            <p className="font-medium text-ink">{videos.errorTitle}</p>
            <p className="mt-1 text-sm text-ink-secondary">{query.error.message}</p>
            <div className="mt-4 flex justify-center">
              <Button size="sm" variant="outline" onClick={query.reload}>
                Try again
              </Button>
            </div>
          </div>
        )}

        {query.status === 'success' && query.data.length === 0 && (
          <p className="text-ink-secondary">{videos.empty}</p>
        )}

        {query.status === 'success' && query.data.length > 0 && (
          <ul className="grid gap-8 sm:grid-cols-2">
            {query.data.map((video) => (
              <li key={video.id}>
                <VideoCard video={video} />
              </li>
            ))}
          </ul>
        )}
      </Container>
    </>
  )
}

function VideoCard({ video }: { video: Video }) {
  const [playing, setPlaying] = useState(false)

  return (
    <figure>
      <div className="relative aspect-video overflow-hidden rounded-card border border-line bg-navy">
        {playing ? (
          <iframe
            src={`${video.embedUrl}?autoplay=1`}
            title={video.title}
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
            className="h-full w-full"
          />
        ) : (
          <button
            type="button"
            onClick={() => setPlaying(true)}
            className="group h-full w-full"
            aria-label={`Play "${video.title}"`}
          >
            <img
              src={video.thumbnailUrl}
              alt=""
              loading="lazy"
              className="h-full w-full object-cover"
            />
            <span className="absolute inset-0 flex items-center justify-center bg-navy/25 transition-colors group-hover:bg-navy/15">
              <span className="flex h-14 w-14 items-center justify-center rounded-full bg-white text-navy shadow-card transition-transform group-hover:scale-105">
                <Icon name="fa-solid fa-play" className="ml-0.5 text-xl" />
              </span>
            </span>
          </button>
        )}
      </div>
      <figcaption className="mt-3 flex items-start justify-between gap-3">
        <h2 className="font-heading text-base font-semibold">{video.title}</h2>
        <ArrowLink to={video.url} className="shrink-0">
          YouTube
        </ArrowLink>
      </figcaption>
    </figure>
  )
}
