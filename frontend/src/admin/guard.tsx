import { Center, Loader } from '@mantine/core'
import { Navigate, Outlet, useLocation } from 'react-router-dom'
import { useSession } from './hooks'

/**
 * Layout route: only lets admins through. The API independently enforces the
 * same thing on every `/api/admin/*` call, so this is purely about UX (send a
 * signed-out visitor to the login screen instead of a broken shell).
 */
export function RequireAdmin() {
  const location = useLocation()
  const { data, isLoading, isError } = useSession()

  if (isLoading) {
    return (
      <Center mih="100dvh">
        <Loader />
      </Center>
    )
  }

  if (isError || !data?.admin) {
    return (
      <Navigate
        to="/admin/login"
        replace
        state={{ from: location.pathname + location.search }}
      />
    )
  }

  return <Outlet />
}
