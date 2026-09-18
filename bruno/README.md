# Portfolio API - Bruno collection

Covers the whole JSON API in `private/api/` - the public endpoints, the auth
flow, and the authenticated `/api/admin/*` surface - plus every failure mode:
anonymous / non-admin / admin, missing & bad CSRF, cross-site login, the
rate-limit lockout, validation (422), and 404 / 405.

## Prerequisites

1. **MySQL** running with the `portfolio` database.
2. **The site** running:

   ```
   php -S localhost:8000 -t public public/router.php
   ```

3. **Test users** seeded:

   ```
   php bruno/seed-users.php
   ```

   This creates `bruno-admin` (admin) and `bruno-user` (non-admin), both with
   password `bruno-dev-pw`. Remove them later with
   `php bruno/seed-users.php --down`.

## Running it

### Bruno app

1. Open this folder (`bruno/`) as a collection.
2. Select the **Local** environment.
3. **Preferences -> turn the cookie jar OFF.** Every request sets its own
   identity through an explicit `Cookie` header; the shared jar would fight that.
4. Run the **00 Setup** folder first (or **Run Collection**). It signs in as the
   admin and the non-admin and stores `sessionAdmin` / `csrfAdmin` /
   `sessionUser` / `csrfUser` as runtime variables that the rest of the
   collection reads. Re-run **00 Setup** if a later folder starts returning 401.

### CLI (headless)

From `bruno/`:

```
npx @usebruno/cli run --env Local
```

Runs every folder in order (Setup first), so runtime variables flow through.

## Folders

| Folder | What it checks |
|---|---|
| `00 Setup` | logs in as admin + non-admin, captures session + CSRF |
| `01 Public` | health / profile / skills / projects / opensource / videos / contact; 404 + 405 |
| `02 Auth` | csrf, login failures, cross-site 403, logout; **`Rate limit/`** trips the 429 lockout |
| `03 Admin access control` | 401 (anon) vs 403 (non-admin) vs 200 (admin); 419 for missing / bad CSRF; the `X-HTTP-Method-Override` path |
| `04 Admin - Projects` | full create -> update -> soft delete -> restore -> hard purge (multipart); validation 422s |
| `05 Admin - GitHub proxy` | repo / languages / contributors / user lookups; bad URL + bad username 422s |
| `06 Admin - Videos` | pin, rename, hide, restore (each restored to its original state); YouTube sync |
| `07 Admin - Open source` | add a repo + its PRs, then delete it; validation 422s |

## Notes

- **`02 Auth/Rate limit`** locks that throwaway session's login for 60 seconds.
  It doesn't touch the admin session, but wait a minute before re-running just
  that folder.
- **`06 Admin - Videos/11 Sync from YouTube`** and everything in
  **`05 Admin - GitHub proxy`** / **`07 Admin - Open source`** make real calls to
  api.github.com / googleapis.com. If `GITHUB_TOKEN` in `.env` is missing or
  expired the GitHub proxy falls back to unauthenticated requests and the tests
  still pass (just at a lower rate limit).
- **`01 Public/15 Contact`** expects **503** because SMTP is unconfigured
  (`$email` is blank in `settings.php`). Wire up SMTP and it becomes `201`.
- The Projects and Open-source flows clean up after themselves. If a run is
  interrupted mid-flow:

  ```sql
  DELETE FROM projects WHERE name = 'Bruno Test';
  DELETE FROM opensource_projects WHERE name = 'octocat/Hello-World';
  ```
