# private/api - the JSON API

Front controller: `bootstrap.php` (included by `public/index.php` for any
`/api/*` path). Routes in `routes.php`, matched by `ApiRouter` (`get/post/put/
patch/delete`, `{param}` -> `[^/]+`, 404 vs 405).

## Wire format

- Success: `{ "data": ..., "meta"?: {...} }` via `ApiResponse::ok/collection/created/noContent`.
- Error: `{ "error": { "message", "code"?, ...extra } }` via `ApiException`.
  Factories: `notFound` 404 `not_found`, `methodNotAllowed` 405, `validation`
  422 `validation_error` (`fields`), `unauthorized` 401, `forbidden` 403.
  Ad-hoc: 419 `csrf`, 429 `rate_limited`, 502 `github_unavailable` /
  `youtube_unavailable`, 503 `contact_unavailable`, 422 `operation_failed`.
- `204` (logout, delete, restore, pin toggles that noContent) send no body.

## The gate (`bootstrap.php`, before `dispatch()`)

1. `X-HTTP-Method-Override: PUT|PATCH|DELETE` honoured only on a POST transport.
2. For a **write** (`!GET/HEAD/OPTIONS`) under `admin/` OR `auth/logout`:
   `Csrf::verify()` - reads header `X-CSRF-Token`, compares to `$_SESSION['_csrf']`.
   **CSRF is checked before auth**, so an unauthenticated admin write is 419, not 401.
3. For any `admin/*` path: `Auth::requireAdmin()` - 401 if signed out, 403 if
   signed in without `admin=1`. So even a bogus `/api/admin/xxx` is 401/403 for
   non-admins, 404 only for a real admin.

`auth/login` is CSRF-exempt (no session yet); guarded by `Auth::assertBrowserOrigin()`
(rejects `Sec-Fetch-Site: cross-site`) + a 5-attempt / 60s session throttle.

## Structure

- `Support/` (glob-loaded): `Auth`, `Csrf`, `GitHub` (server-side proxy, token
  from `controllers/Env.php`, falls back to unauthenticated on 401), `Media`,
  `Resource` (DB row -> stable camelCase DTOs).
- `Controllers/` public (`MetaApi`, `ProjectsApi`, `OpenSourceApi`, `VideosApi`,
  `ContactApi`) + top-level `AuthApi`.
- `Controllers/Admin/` (`namespace Admin`, glob-loaded): `DashboardApi`, `MetaApi`,
  `ProjectsApi`, `VideosApi`, `OpenSourceApi`, `GitHubApi`. These are thin
  adapters over `private/controllers/Projects|Videos|OpenSource.php` (which return
  `""` / error-string; wrap non-empty as `ApiException(422, $s, 'operation_failed')`).

## Session keys

`$_SESSION['userId']` = logged-in user id, `$_SESSION['admin']` = admin user id
(only set when `users.admin === 1`). `session_regenerate_id(true)` on login/logout
(changes `PHPSESSID`, keeps `_csrf`).

Full endpoint + failure-mode reference: run the `bruno/` collection.
