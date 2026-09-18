/// <reference types="vite/client" />

interface ImportMetaEnv {
  /** Base URL for the PHP JSON API. Defaults to `/api` (same origin). */
  readonly VITE_API_BASE?: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
