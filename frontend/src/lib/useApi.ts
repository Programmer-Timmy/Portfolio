import { useCallback, useEffect, useState } from 'react'
import { api, ApiError } from './api'

type AsyncState<T> =
  | { status: 'loading'; data: undefined; error: undefined }
  | { status: 'success'; data: T; error: undefined }
  | { status: 'error'; data: undefined; error: ApiError }

/**
 * Small data-fetching hook for GET endpoints. Deliberately dependency-free,
 * swap for TanStack Query if caching/refetching needs grow.
 */
export function useApi<T>(path: string | null): AsyncState<T> & { reload: () => void } {
  const [tick, setTick] = useState(0)
  const [state, setState] = useState<AsyncState<T>>({
    status: 'loading',
    data: undefined,
    error: undefined,
  })

  const reload = useCallback(() => setTick((t) => t + 1), [])

  useEffect(() => {
    if (path === null) return
    let alive = true
    setState({ status: 'loading', data: undefined, error: undefined })

    api
      .get<T>(path)
      .then((data) => {
        if (alive) setState({ status: 'success', data, error: undefined })
      })
      .catch((error: unknown) => {
        if (!alive) return
        const apiError =
          error instanceof ApiError
            ? error
            : new ApiError(0, 'Something went wrong.', 'unknown')
        setState({ status: 'error', data: undefined, error: apiError })
      })

    return () => {
      alive = false
    }
  }, [path, tick])

  return { ...state, reload }
}
