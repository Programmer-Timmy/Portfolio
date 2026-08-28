# Portfolio Frontend

React + TypeScript (TSX) + Vite + Tailwind v4. This is the new frontend for the
portfolio, being migrated page by page off the server-rendered PHP views. The
PHP app stays as the backend and is treated as a **same-origin JSON API**.

## Why same-origin (SSO / auth)

The existing admin auth is PHP session based (`$_SESSION['admin']`, and whatever
SSO you layer on later). Serving this SPA from the same origin as PHP means the
session cookie is sent automatically with every `fetch`. No CORS, no tokens, no
cross-site-cookie problems. The client just calls `fetch('/api/...', { credentials: 'include' })`
(wrapped in `src/lib/api.ts`) and the PHP session remains the single source of
truth for who is logged in.

Concretely:

- **Dev:** Vite proxies `/api`, `/img` and `/doc` to the PHP server
  (`VITE_PHP_ORIGIN`, default `http://localhost:8000`). Run PHP with
  `php -S localhost:8000 -t public public/router.php`.
- **Prod:** `npm run build` outputs to `../public/app/` (asset URLs are absolute
  under `/app/`). Serve `public/` from PHP as usual, nothing else to configure.

## How PHP serves the SPA

Every request still goes through `public/index.php` first, so session, SSO,
maintenance and auth checks run before anything is sent. Then:

1. `/api/*` -> the JSON API (`private/api/`).
2. A path listed in `$site['spa']['routes']` (settings.php) -> PHP outputs the
   built `public/app/index.html` and React takes over client-side (`Spa` class).
3. Anything else -> the existing PHP view.

So migrated and not-yet-migrated pages coexist on one origin. Links follow the
same split: `AppLink` / `Button` render a client-side `<Link>` for a route in
`src/lib/migrated.ts` and a plain `<a>` (full navigation to PHP) for the rest.

### Migrating a page

1. Build the page component and add its route in `src/router.tsx`.
2. Add the path to `MIGRATED_ROUTES` in `src/lib/migrated.ts`.
3. Add the same path to `$site['spa']['routes']` in `private/config/settings.php`.
4. Add its `/api/...` endpoint if it needs data; type the response in `types.ts`.
5. `npm run build`.

## REST API

Lives in `private/api/`, reached at `/api/*` (routed from `public/index.php`
before the page/admin logic). Success bodies are `{ "data", "meta"? }`; errors
are `{ "error": { "message", "code"?, "fields"? } }`.

| Method and path | Purpose |
| --- | --- |
| `GET /api/health` | service check |
| `GET /api/profile` | full "about me" payload (from `private/config/profile.php`) |
| `GET /api/skills` | skills groups |
| `GET /api/auth/session` | `{ authenticated, admin }` from the PHP session |
| `GET /api/projects?featured=true&limit=3` | project collection |
| `GET /api/projects/{id}` | project, gallery, languages, contributors |
| `GET /api/opensource` | contributed repos with PR counts |
| `GET /api/opensource/{id}` | repo plus pull requests |
| `GET /api/videos?limit=6` | video collection |
| `POST /api/contact` | `{ name, email, message }`; returns 503 until SMTP is set in `settings.php` |

Response types are in `src/lib/types.ts`. Fetch with `useApi()` or the `api`
helper in `src/lib/api.ts`.

## Commands

```bash
npm install
cp .env.example .env
npm run dev        # http://localhost:5173
npm run build
npm run typecheck
npm run lint
```

## Layout

```
src/
  main.tsx                 app entry + RouterProvider
  router.tsx               routes (real pages + PlaceholderPage stubs)
  styles/index.css         Tailwind v4 + design tokens from STYLEGUIDE.md
  lib/
    api.ts                 fetch wrapper for the PHP JSON API
    theme.ts               light/dark toggle (data-theme on <html>)
    cn.ts                  className joiner
  components/
    layout/                SiteHeader, SiteFooter, RootLayout, nav config
    ui/                    Button, Card, Badge, Container, Logo, PageHeading
  pages/                   HomePage, NotFoundPage, PlaceholderPage
public/brand/              logo set (English wordmark + universal <TK/> icon)
STYLEGUIDE.md              the brand style guide these tokens come from
```

## Design tokens

Defined in `src/styles/index.css` under `@theme`, straight from `STYLEGUIDE.md`:

- Colours: `navy`, `teal`, `teal-light`, `paper`, `surface`, `line`,
  `ink` / `ink-secondary` / `ink-muted`, and the `status-*` set (server/status
  pages only. Keep them off marketing content).
- Fonts: `font-heading` (Poppins), `font-sans` (Inter), `font-mono` (JetBrains Mono).
- Text sizes: `text-h1` / `text-h2` / `text-h3`. Radius: `rounded-card`.

Rule of thumb from the guide: **navy is for reading, teal is for clicking.**
