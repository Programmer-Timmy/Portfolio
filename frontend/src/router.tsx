import { createBrowserRouter } from 'react-router-dom'
import { RootLayout } from '@/components/layout/RootLayout'
import { HomePage } from '@/pages/HomePage'
import { NotFoundPage } from '@/pages/NotFoundPage'
import { AboutPage } from '@/pages/AboutPage'
import { ContactPage } from '@/pages/ContactPage'

/**
 * Only routes React actually owns live here. Everything else is still served
 * by the PHP views, and links to those pages do a full navigation (see
 * `src/lib/migrated.ts` + `AppLink`).
 *
 * To migrate a page: add its route below, add the path to `MIGRATED_ROUTES`
 * and to `$site['spa']['routes']` in private/config/settings.php.
 */
export const router = createBrowserRouter([
  {
    element: <RootLayout />,
    children: [
      { index: true, element: <HomePage /> },
      { path: 'about', element: <AboutPage /> },
      { path: 'contact', element: <ContactPage /> },
      { path: '*', element: <NotFoundPage /> },
    ],
  },
])
