# bruno/ - API test collection

Bruno collection covering the whole `private/api/` surface plus every failure
mode (401 / 403 / 419 / 422 / 429 / 404 / 405). ~89 requests, all green.

## Run

```bash
php bruno/seed-users.php                              # bruno-admin / bruno-user, pw bruno-dev-pw
cd bruno && npx @usebruno/cli run --env Local         # headless, runs 00 Setup first
```

In the Bruno app: pick the **Local** env, **turn the cookie jar OFF**, run
**00 Setup** first. `php bruno/seed-users.php --down` to remove the users.

## How it's wired

- **Identity is explicit per request.** The shared cookie jar can't hold
  admin + non-admin + anon at once, so every authenticated request sends
  `headers { Cookie: {{sessionAdmin}} }` (or `{{sessionUser}}`, or nothing).
  `collection.bru` pre-request runs `bru.cookies.clear()` as a backup.
- `00 Setup` logs in each identity and parses `PHPSESSID` from
  `res.getHeader('set-cookie')` into runtime vars `sessionAdmin` / `csrfAdmin` /
  `sessionUser` / `csrfUser`. **Login before CSRF** - login regenerates the
  session id but keeps `_csrf`.
- Writes add `headers { X-CSRF-Token: {{csrfAdmin}} }`.
- The Projects and Open-source flows create then delete their own data
  (`04 Admin - Projects` ends with `?hard=1` purge). Videos are toggled/renamed
  then restored. Nothing is left behind.

## Editing

- `.bru` v2. Multipart: method block `body: multipartForm`, body block
  `body:multipart-form { field: @file(files/x.png||files/y.png) }` - **`@file()`
  paths are relative to `bruno/` (the collection root)**, not the request folder.
- Assert ops: `eq neq isDefined isArray isNumber isString matches length contains`.
  Bare value = `eq`.
- `bru.setVar` in `script:post-response` for extractions with logic;
  `vars:post-response { x: res.body.data.x }` for simple ones.
- JS comparisons: `tests { test("...", function () { expect(...).to.equal(...) }) }`.
- When the API changes, the assertions here mirror exact status codes + messages
  from `private/api/Controllers/**` - update both together.
