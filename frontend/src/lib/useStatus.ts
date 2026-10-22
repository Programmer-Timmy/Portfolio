import { useEffect, useState } from 'react'
import { api } from './api'
import type { StatusPage } from './types'

export type StatusSource =
  | { slug: string; page: StatusPage; error?: undefined }
  | { slug: string; page?: undefined; error: string }

const REFRESH_MS = 60_000

export function useStatus() {
  const [state, setState] = useState<{ sources: StatusSource[]; error?: string; loading: boolean; updatedAt?: Date }>({
    sources: [],
    loading: true,
  })

  useEffect(() => {
    let alive = true

    const load = async () => {
      try {
        const slugs = await api.get<string[]>('/status')
        const results = await Promise.allSettled(
          slugs.map((slug) => api.get<StatusPage>(`/status/${slug}`)),
        )
        const sources = results.map((result, i): StatusSource =>
          result.status === 'fulfilled'
            ? { slug: slugs[i], page: result.value }
            : { slug: slugs[i], error: result.reason?.message ?? 'Unavailable.' },
        )
        if (alive) setState({ sources, loading: false, updatedAt: new Date() })
      } catch (e) {
        if (alive) setState({ sources: [], error: (e as Error).message, loading: false })
      }
    }

    load()
    const timer = setInterval(load, REFRESH_MS)
    return () => {
      alive = false
      clearInterval(timer)
    }
  }, [])

  return state
}
