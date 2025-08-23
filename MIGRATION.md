# Migration: Legacy site to CodeIgniter 4 at repo root

This repository was restructured to promote the CodeIgniter 4 application to the repository root and archive the legacy PHP site.

Date: 2025-08-22
Branch: promote-ci4-to-root (or fix-promote-ci4-to-root)

## What changed
- CodeIgniter 4 app moved from `cta-ci-app/` to the repository root.
- Legacy files moved into `legacy-site/` for archival.
- Web server DocumentRoot should now point to `public/` at the repository root.

## New layout
```
.git/
.gitignore
.env
composer.json
composer.lock
LICENSE
README.md
MIGRATION.md
spark
app/
public/
tests/
vendor/
writable/
preload.php
phpunit.xml.dist
builds/        (if present)
legacy-site/   (archived legacy site)
```

## Local run
- Install deps: `composer install`
- Copy `env` to `.env` (or ensure `.env` exists), set `app.baseURL`
- Development server: `php spark serve -port 8080`

## Notes
- Keep `legacy-site/` in the repo (or separate archived branch) for history.
- Add 301 redirects at the web server if old URLs need to map to new routes.