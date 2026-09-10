# suttsc.com — project guidelines

Drop this at `.ai/guidelines/suttsc.md`. Laravel Boost composes it into the generated
`CLAUDE.md`. Do not hand-edit `CLAUDE.md` or `AGENTS.md` — `boost:update` regenerates them.

## What this application is

A personal site built as a small operating system for one person. The homepage is a
Raycast-style command palette; commands answer inline, show a numbered menu, open a
draggable window, or navigate to a real page. A taskbar carries the sitename, a back
button, minimised windows and the owner's local time.

It is not a CMS and not a portfolio template. There is one user and there will only
ever be one user.

## Who writes the code

Chris does. When he asks how something should work, give the design and the reasoning
first. Write code when he asks for code, when you're fixing something specific, or when
a snippet is the clearest way to make a point — not as the default response to a
question. Don't scaffold whole features unprompted.

## The command registry — the load-bearing abstraction

Everything hangs off this. Get it right and adding a command is a twenty-minute job.

- One class per command in `app/Terminal/Commands/`, auto-discovered
- Implements `TerminalCommand`: `signature()`, `description()`, `hidden()`,
  `authorized()`, `handle()`
- `handle()` returns exactly one of four output DTOs: `Inline`, `Menu`, `Window`,
  `Navigate`. A typed union — never an array, never a bare string
- Every command has a feature test. The registry is the contract
- The registry serialises to a manifest shipped in Inertia's shared props so the client
  filters with zero latency. **Commands failing `authorized()` are stripped from the
  manifest server-side** — never filtered in React, never present in the page source

When adding a command, follow the shape of the existing ones exactly. Consistency here
matters more than cleverness in any individual command.

## Hard rules

**Never call a third-party API during a request cycle.** Scheduled jobs pull into local
mirror tables; requests read local data only. This includes the `<title>` fetch when a
link is saved — that's a queued job. If a feature seems to need a synchronous external
call, stop and say so rather than working around it.

**Never persist anything identifying about a visitor.** No emails, names, avatars, IPs
or fingerprints in any table, log or cache. `/whoami` reads browser and timezone at
render time and discards them. A rejected Google sign-in flashes the person their own
details for a single render and discards the payload. The only thing that persists is
`site_stats.nice_try_count`, a bare integer. If a requested feature needs visitor data
stored, refuse and explain — this is a design rule, not a preference.

**Every command that displays content has a real server-rendered route.** The palette is
a layer over a normal site, not a replacement for one. Shareable URLs, crawlable,
works without JS.

**No admin panel.** No Filament, no dashboard, no CRUD scaffolding. Records are added
through commands gated by `authorized()`. The palette is the back office.

## Auth

No `users` table and no migration for one. `config('auth.owner_email')` holds the single
allowed address; a custom `UserProvider` backed by that config supplies `Auth::check()`,
`auth` middleware, gates and policies with no database behind them.

- `Owner implements Authenticatable` — identifier is the string `'owner'`, password is
  `''`, remember-token methods are no-ops
- `OwnerUserProvider implements UserProvider` — `retrieveById()` returns an `Owner` for
  `'owner'` and null otherwise; `retrieveByCredentials()`/`validateCredentials()` return
  null/false; `rehashPasswordIfRequired()` is a no-op
- After the Socialite callback: normalise and compare the email with `hash_equals`
  against the config value, exact match only. Never a domain check, never
  `str_contains`. Then `Auth::login(new Owner())`
- Socialite stays **stateful** — the state parameter is the CSRF protection
- Sessions must use the `database` or encrypted `cookie` driver, never `file`

## PHP conventions

- PHP 8.4+, `declare(strict_types=1)` in every file
- Constructor property promotion, readonly where it fits, backed enums over constants
- Thin controllers: Form Requests validate, Actions/Services hold logic, API Resources
  shape output
- Integrations behind an interface with a fake implementation bound in tests
- Pest, not PHPUnit syntax. Feature tests over unit tests for anything with a route
- Laravel Pint before committing

## Front end

- Inertia + React + TypeScript. Tailwind for all styling — no bespoke CSS files unless
  genuinely unavoidable
- The palette, window stack and taskbar live in a **persistent Inertia layout** above the
  page component, so open windows survive navigation
- The palette is a combobox: `role="combobox"`, `aria-expanded`, `aria-activedescendant`,
  results as `role="listbox"`/`role="option"`. Focus trapped in an open window and
  returned on close. Respect `prefers-reduced-motion`
- React components render output DTOs. No business logic in them
- Resist building a window manager. Drag, focus, close, minimise. No tiling, no snapping,
  no resize handles until something actually needs them

## Tone of user-facing copy

Deadpan. The site behaves like a real OS that has quietly decided you are Chris. One dry
line beats three — if a joke needs a setup and a punchline, it's too long. When writing
copy, pitch two or three options rather than committing to one.

## Environment

WSL2, PhpStorm on Windows. Project lives in the Linux filesystem. Run artisan, composer,
npm and tests inside WSL. See `claude/dev-setup.md` in the Cowork project for the full
setup.
