import { useEffect } from 'react'
import { Outlet, ScrollRestoration, useLocation } from 'react-router-dom'
import { SiteHeader } from './SiteHeader'
import { SiteFooter } from './SiteFooter'

export function RootLayout() {
  const { pathname } = useLocation()

  // Move keyboard focus to the main region on navigation.
  useEffect(() => {
    document.getElementById('main')?.focus({ preventScroll: true })
  }, [pathname])

  return (
    <div className="flex min-h-dvh flex-col">
      <a
        href="#main"
        className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-teal focus:px-4 focus:py-2 focus:text-white"
      >
        Skip to content
      </a>
      <SiteHeader />
      <main id="main" tabIndex={-1} className="flex-1 outline-none">
        <Outlet />
      </main>
      <SiteFooter />
      <ScrollRestoration />
    </div>
  )
}
