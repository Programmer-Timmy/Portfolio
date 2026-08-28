/**
 * Thin client for the PHP JSON API.
 *
 * The React app is served from the same origin as PHP, so the PHP session
 * cookie (and any SSO session it manages) rides along automatically with
 * `credentials: 'include'`. No tokens to juggle on the client.
 *
 * When you add API endpoints on the backend, return JSON like:
 *   { "data": ... }            on success
 *   { "error": "message" }     on failure (with a 4xx/5xx status)
 */

const API_BASE = import.meta.env.VITE_API_BASE ?? '/api'

export class ApiError extends Error {
  status: number

  constructor(status: number, message: string) {
    super(message)
    this.name = 'ApiError'
    this.status = status
  }
}

export async function apiFetch<T>(
  path: string,
  init: RequestInit = {},
): Promise<T> {
  const res = await fetch(`${API_BASE}${path}`, {
    credentials: 'include',
    ...init,
    headers: {
      Accept: 'application/json',
      ...(init.body ? { 'Content-Type': 'application/json' } : {}),
      ...init.headers,
    },
  })

  const isJson = res.headers
    .get('content-type')
    ?.includes('application/json')
  const payload = isJson ? await res.json() : null

  if (!res.ok) {
    const message =
      (payload && typeof payload === 'object' && 'error' in payload
        ? String(payload.error)
        : null) ?? `Request failed (${res.status})`
    throw new ApiError(res.status, message)
  }

  return (payload && typeof payload === 'object' && 'data' in payload
    ? payload.data
    : payload) as T
}

export const api = {
  get: <T>(path: string) => apiFetch<T>(path),
  post: <T>(path: string, body?: unknown) =>
    apiFetch<T>(path, {
      method: 'POST',
      body: body === undefined ? undefined : JSON.stringify(body),
    }),
}
