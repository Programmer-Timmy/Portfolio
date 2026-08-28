import { useEffect, useState } from 'react'

export type Theme = 'light' | 'dark'

const STORAGE_KEY = 'tk-theme'

function readInitialTheme(): Theme {
  if (typeof window === 'undefined') return 'light'
  const stored = window.localStorage.getItem(STORAGE_KEY)
  if (stored === 'light' || stored === 'dark') return stored
  // Styleguide default: marketing pages are light. Only honour an explicit
  // OS "dark" preference so night readers still get the dark palette.
  return window.matchMedia?.('(prefers-color-scheme: dark)').matches
    ? 'dark'
    : 'light'
}

/**
 * App-wide light/dark toggle. Writes `data-theme` on <html>, which is what
 * the token overrides in index.css key off.
 */
export function useTheme() {
  const [theme, setTheme] = useState<Theme>(readInitialTheme)

  useEffect(() => {
    document.documentElement.dataset.theme = theme
    window.localStorage.setItem(STORAGE_KEY, theme)
  }, [theme])

  return {
    theme,
    setTheme,
    toggle: () => setTheme((t) => (t === 'light' ? 'dark' : 'light')),
  }
}
