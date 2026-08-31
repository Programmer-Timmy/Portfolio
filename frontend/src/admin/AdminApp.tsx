// Mantine styles, layer-wrapped so Tailwind's preflight (in the `base` layer)
// can't override admin components. The `@layer` order is declared in
// src/styles/index.css. These imports live here — inside the lazily-loaded
// admin entry — so the CSS never ships in the public bundle.
import '@mantine/core/styles.layer.css'
import '@mantine/notifications/styles.layer.css'

import { MantineProvider } from '@mantine/core'
import { Notifications } from '@mantine/notifications'
import { ModalsProvider } from '@mantine/modals'
import { QueryClientProvider } from '@tanstack/react-query'
import { Route, Routes } from 'react-router-dom'
import { adminTheme } from './adminTheme'
import { queryClient } from './lib/queryClient'
import { RequireAdmin } from './guard'
import { AdminShell } from './components/AdminShell'
import { LoginPage } from './pages/LoginPage'
import { DashboardPage } from './pages/DashboardPage'
import { ProjectsListPage } from './pages/ProjectsListPage'
import { GitHubTestPage } from './pages/_GitHubTestPage'
import { AdminNotFound } from './pages/AdminNotFound'

/**
 * Root of the admin SPA. Mounted by a single lazy route (`/admin/*` in
 * src/router.tsx), so every provider and all Mantine CSS is scoped to this
 * subtree and code-split away from the public site.
 *
 * Routes are descendant `<Routes>` (not data-router children) because the admin
 * fetches through TanStack Query, not router loaders. Paths are relative to
 * `/admin`.
 */
export function AdminApp() {
  return (
    <MantineProvider theme={adminTheme} defaultColorScheme="light">
      <QueryClientProvider client={queryClient}>
        <ModalsProvider>
          <Notifications position="top-right" />
          <Routes>
            <Route path="login" element={<LoginPage />} />
            <Route element={<RequireAdmin />}>
              <Route element={<AdminShell />}>
                <Route index element={<DashboardPage />} />
                <Route path="projects" element={<ProjectsListPage />} />
                {/* TEMP (M3): remove with pages/_GitHubTestPage.tsx once M4's form uses GitHubAutofill */}
                <Route path="_github-check" element={<GitHubTestPage />} />
                <Route path="*" element={<AdminNotFound />} />
              </Route>
            </Route>
          </Routes>
        </ModalsProvider>
      </QueryClientProvider>
    </MantineProvider>
  )
}
