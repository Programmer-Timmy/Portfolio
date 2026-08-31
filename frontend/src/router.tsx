import { createBrowserRouter } from 'react-router-dom'
import { RootLayout } from '@/components/layout/RootLayout'
import { HomePage } from '@/pages/HomePage'
import { NotFoundPage } from '@/pages/NotFoundPage'
import { AboutPage } from '@/pages/AboutPage'
import { ContactPage } from '@/pages/ContactPage'
import { ProjectsPage } from '@/pages/ProjectsPage'
import { ProjectPage } from '@/pages/ProjectPage'

/**
 * Only routes React actually owns live here. Everything else is still served
 * by the PHP views, and links to those pages do a full navigation (see
 * `src/lib/migrated.ts` + `AppLink`).
 *
 * To migrate a page: add its route below, add the path to `MIGRATED_ROUTES`
 * and to `$site['spa']['routes']` in private/config/settings.php.
 *
 * The admin app (`/admin/*`) is a single lazy route: its Mantine + TanStack
 * Query providers and CSS are code-split out of the public bundle.
 */
export const router = createBrowserRouter([
  {
    element: <RootLayout />,
    children: [
      { index: true, element: <HomePage /> },
      { path: 'about', element: <AboutPage /> },
      { path: 'contact', element: <ContactPage /> },
      { path: 'projects', element: <ProjectsPage /> },
      { path: 'project/:id', element: <ProjectPage /> },
      { path: '*', element: <NotFoundPage /> },
    ],
  },
  {
    path: 'admin/*',
    lazy: async () => {
      const { AdminApp } = await import('@/admin/AdminApp')
      return { Component: AdminApp }
    },
  },
])
