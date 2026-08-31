/**
 * Routes that the React app owns. Everything else is still rendered by the PHP
 * views, so links to those must be full-page navigations (a plain <a>), not
 * client-side <Link> transitions.
 *
 * Keep this in sync with `$site['spa']['routes']` in private/config/settings.php.
 * When you migrate a page: add its path here AND to the PHP list.
 */
// '/projects' is the list page. The detail page ('/project/{id}', singular) is
// still served by PHP, so keep it off this list.
export const MIGRATED_ROUTES: string[] = ['/', '/about', '/contact', '/projects']

export function isMigrated(path: string): boolean {
  const clean = path.split(/[?#]/)[0]
  return MIGRATED_ROUTES.some(
    (route) =>
      route === clean ||
      (route !== '/' && (clean === route || clean.startsWith(`${route}/`))),
  )
}
