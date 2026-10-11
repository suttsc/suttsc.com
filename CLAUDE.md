<laravel-boost-guidelines>
=== .ai/suttsc rules ===

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

=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>
