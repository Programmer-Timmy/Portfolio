/**
 * Client for the PHP JSON REST API (see private/api/).
 *
 * The React app is served from the same origin as PHP, so the PHP session
 * cookie (and any SSO session it manages) rides along automatically with
 * `credentials: 'include'`. No tokens to juggle on the client.
 *
 * Wire format:
 *   success     { "data": ..., "meta"?: {...} }
 *   error        { "error": { "message": string, "code"?: string, "fields"?: {...} } }
 */

const API_BASE = import.meta.env.VITE_API_BASE ?? '/api'

export class ApiError extends Error {
  status: number
  code?: string
  fields?: Record<string, string>

  constructor(
    status: number,
    message: string,
    code?: string,
    fields?: Record<string, string>,
  ) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.code = code
    this.fields = fields
  }
}

export type ApiEnvelope<T> = { data: T; meta?: Record<string, unknown> }

async function request<T>(path: string, init: RequestInit = {}): Promise<ApiEnvelope<T>> {
  let res: Response
  try {
    res = await fetch(`${API_BASE}${path}`, {
      credentials: 'include',
      ...init,
      headers: {
        Accept: 'application/json',
        ...(init.body ? { 'Content-Type': 'application/json' } : {}),
        ...init.headers,
      },
    })
  } catch {
    throw new ApiError(0, 'Could not reach the server. Check your connection and try again.', 'network_error')
  }

  const isJson = res.headers.get('content-type')?.includes('application/json')
  const payload: unknown = isJson ? await res.json().catch(() => null) : null

  if (!res.ok) {
    const err =
      payload && typeof payload === 'object' && 'error' in payload
        ? (payload as { error: { message?: string; code?: string; fields?: Record<string, string> } }).error
        : null
    throw new ApiError(
      res.status,
      err?.message ?? `Request failed (${res.status})`,
      err?.code,
      err?.fields,
    )
  }

  if (payload && typeof payload === 'object' && 'data' in payload) {
    return payload as ApiEnvelope<T>
  }
  return { data: payload as T }
}

export const api = {
  /** Returns the `data` field. */
  get: <T>(path: string) => request<T>(path).then((r) => r.data),
  /** Returns the full `{ data, meta }` envelope. */
  getEnvelope: <T>(path: string) => request<T>(path),
  post: <T>(path: string, body?: unknown) =>
    request<T>(path, {
      method: 'POST',
      body: body === undefined ? undefined : JSON.stringify(body),
    }).then((r) => r.data),
}
