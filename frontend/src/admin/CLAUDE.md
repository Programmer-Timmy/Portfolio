# frontend/src/admin - the Mantine admin

A self-contained admin app under `/admin/*`, code-split out of the public bundle.
Mounted by one lazy route in `src/router.tsx` (`{ path: 'admin/*', lazy: () =>
import('@/admin/AdminApp') }`). Public pages never load any of this.

## Rules

- **Mantine only here.** Public pages use `src/components/ui/`. Never import
  Mantine outside `src/admin/`, never import the public `ui/` kit into admin.
- Mantine CSS is imported (layer-wrapped) in `AdminApp.tsx`. `src/styles/index.css`
  declares `@layer theme, base, mantine, components, utilities;` so Tailwind
  preflight can't clobber Mantine. Admin is forced light mode.
- Data: **TanStack Query** (`lib/queryClient.ts`, provider in `AdminApp`). Public
  pages keep `useApi`. Hooks live in `hooks.ts`, keys in `lib/queryKeys.ts`.
- API calls: `lib/adminApi.ts` (`get/post/patch/del/postForm`) - handles the CSRF
  token (fetched once from `/api/auth/csrf`, cached, refetched once on 419).
- Forms: react-hook-form + zod (`lib/schemas.ts`). Rich text: `QuillEditor.tsx`
  wraps raw `quill@2` (react-quill doesn't do React 19); its toolbar must stay a
  subset of what `src/components/DeltaContent.tsx` can render. Project
  descriptions are stored as Quill delta ops.

## Structure

`AdminApp.tsx` = providers + descendant `<Routes>` (not data-router children).
`guard.tsx` `<RequireAdmin>` checks `/api/auth/session`. `components/AdminShell.tsx`
= Mantine AppShell + nav. Pages: `LoginPage`, `DashboardPage`, `ProjectsListPage`,
`ProjectFormPage` (new + `:id` share it), `VideosListPage`, `OpenSourceListPage`,
`OpenSourceAddPage`.

## Adding a screen

1. Page component in `pages/`, route in `AdminApp.tsx`, nav item in `AdminShell.tsx`.
2. Add the path to `$site['spa']['routes']` in `private/config/settings.php` AND
   (it's covered by the `/admin` prefix already in) `src/lib/migrated.ts`.
3. `npm run typecheck && npm run lint && npm run build`.

## Watch out

- `projects.name` is `varchar(20)` in the DB - the form caps the title at 20.
- The image-upload path is `$site['paths']['webroot'] . '/img'`; override
  `webroot` if the production doc root isn't `public/`.
- `GITHUB_TOKEN` in `.env` may be expired - the GitHub proxy falls back to
  unauthenticated calls (lower rate limit) so things still work.

Milestones + history: `~/.claude/.../memory/admin-rebuild.md`. M7 (delete the old
PHP `private/views/pages/admin/*` + `login.php`) is all that's left.
