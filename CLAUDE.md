# Portfolio - repo guide

Personal portfolio site for Tim van der Kloet, mid-migration from a custom PHP
MVC app to a React SPA that treats PHP as a same-origin JSON API.

## Layout

| Path | What |
|---|---|
| `public/` | web root. `index.php` is the single front controller; `router.php` mirrors it for `php -S`. Built SPA lands in `public/app/` (gitignored). Uploads in `public/img/` (gitignored). |
| `private/` | PHP app. `config/settings.php` (`$site`, `$database`, `$email`), `controllers/` (static service classes + hand-rolled PDO `Database`), `views/` (legacy server-rendered pages), `routes.php` (legacy router). |
| `private/api/` | the JSON API. See `private/api/CLAUDE.md`. |
| `frontend/` | Vite + React 19 + TS + Tailwind v4 SPA. Migrated pages listed in `src/lib/migrated.ts` (keep in sync with `$site['spa']['routes']`). |
| `frontend/src/admin/` | the new Mantine admin under `/admin/*`. See its `CLAUDE.md`. |
| `bruno/` | API test collection. See `bruno/CLAUDE.md`. |

## Dev

```bash
php -S localhost:8000 -t public public/router.php   # + MySQL (db 'portfolio', root / no password)
cd frontend && npm run dev                          # Vite on :5173, proxies /api /img /doc to :8000
```

Before finishing frontend work: `cd frontend && npm run typecheck && npm run lint && npm run build`
(build emits to `public/app/`). PHP: `php -l <file>` on anything touched.

## Conventions

- No em-dashes anywhere (Tim's preference) - use ` - `, a colon, or parens.
- PHP has no framework, no Composer for app code, no migrations. `Database` is a
  static query builder; table/column names are interpolated, values are bound.
- The frontend has two worlds: public pages use the hand-rolled `src/components/ui/`
  Tailwind kit; the admin uses Mantine (scoped to `/admin` only). Don't mix them.
- Commits: Tim makes one per feature/milestone, plain imperative subject +
  short body. Only commit/push when asked.

## In flight

Admin rebuild (see `frontend/src/admin/CLAUDE.md`): M0-M6 done, M7 (retire the old
PHP `private/views/pages/admin/*` + `login.php`) is the only milestone left.
