import { ApiError } from '@/lib/api'

/**
 * API client for the authenticated `/api/admin/*` surface plus the auth
 * endpoints. Separate from `src/lib/api.ts` because only the admin needs CSRF
 * plumbing, FormData bodies and PUT/PATCH/DELETE — keeping them here keeps the
 * public bundle lean. Errors are the same `ApiError` the public client throws.
 *
 * Wire format matches the rest of the API: `{ data, meta? }` on success,
 * `{ error: { message, code?, fields? } }` on failure.
 */

const API_BASE = import.meta.env.VITE_API_BASE ?? '/api'

/** Session CSRF token, fetched once and cached in module scope. */
let csrfToken: string | null = null

export function clearCsrfToken(): void {
  csrfToken = null
}

async function parse<T>(res: Response): Promise<T> {
  const isJson = res.headers.get('content-type')?.includes('application/json')
  const payload: unknown = isJson ? await res.json().catch(() => null) : null

  if (!res.ok) {
    const err =
      payload && typeof payload === 'object' && 'error' in payload
        ? (payload as {
            error: { message?: string; code?: string; fields?: Record<string, string> }
          }).error
        : null
    throw new ApiError(
      res.status,
      err?.message ?? `Request failed (${res.status})`,
      err?.code,
      err?.fields,
    )
  }

  if (payload && typeof payload === 'object' && 'data' in payload) {
    return (payload as { data: T }).data
  }
  return payload as T
}

async function get<T>(path: string): Promise<T> {
  let res: Response
  try {
    res = await fetch(`${API_BASE}${path}`, {
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })
  } catch {
    throw new ApiError(0, 'Could not reach the server. Check your connection and try again.', 'network_error')
  }
  return parse<T>(res)
}

async function ensureCsrf(): Promise<string> {
  if (!csrfToken) {
    const { token } = await get<{ token: string }>('/auth/csrf')
    csrfToken = token
  }
  return csrfToken
}

type Method = 'POST' | 'PATCH' | 'PUT' | 'DELETE'

async function mutate<T>(
  path: string,
  method: Method,
  body?: unknown,
  form?: FormData,
  allowRetry = true,
): Promise<T> {
  const token = await ensureCsrf()
  const isForm = form !== undefined
  const hasJsonBody = !isForm && body !== undefined

  let res: Response
  try {
    res = await fetch(`${API_BASE}${path}`, {
      method,
      credentials: 'include',
      headers: {
        Accept: 'application/json',
        'X-CSRF-Token': token,
        ...(hasJsonBody ? { 'Content-Type': 'application/json' } : {}),
      },
      body: isForm ? form : hasJsonBody ? JSON.stringify(body) : undefined,
    })
  } catch {
    throw new ApiError(0, 'Could not reach the server. Check your connection and try again.', 'network_error')
  }

  // Stale token — refetch once and retry.
  if (res.status === 419 && allowRetry) {
    csrfToken = null
    return mutate<T>(path, method, body, form, false)
  }

  return parse<T>(res)
}

export const adminApi = {
  get,
  post: <T>(path: string, body?: unknown) => mutate<T>(path, 'POST', body),
  patch: <T>(path: string, body?: unknown) => mutate<T>(path, 'PATCH', body),
  put: <T>(path: string, body?: unknown) => mutate<T>(path, 'PUT', body),
  del: <T>(path: string) => mutate<T>(path, 'DELETE'),
  postForm: <T>(path: string, form: FormData) => mutate<T>(path, 'POST', undefined, form),
}
