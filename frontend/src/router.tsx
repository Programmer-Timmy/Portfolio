import { createBrowserRouter } from 'react-router-dom'
import { RootLayout } from '@/components/layout/RootLayout'
import { HomePage } from '@/pages/HomePage'
import { NotFoundPage } from '@/pages/NotFoundPage'
import { PlaceholderPage } from '@/pages/PlaceholderPage'

export const router = createBrowserRouter([
  {
    element: <RootLayout />,
    children: [
      { index: true, element: <HomePage /> },
      { path: 'about', element: <PlaceholderPage title="About" /> },
      { path: 'projects', element: <PlaceholderPage title="Projects" /> },
      { path: 'projects/:id', element: <PlaceholderPage title="Project" /> },
      { path: 'opensource', element: <PlaceholderPage title="Open Source" /> },
      { path: 'opensource/:id', element: <PlaceholderPage title="Open Source Contribution" /> },
      { path: 'videos', element: <PlaceholderPage title="Videos" /> },
      { path: 'contact', element: <PlaceholderPage title="Contact" /> },
      { path: '*', element: <NotFoundPage /> },
    ],
  },
])
