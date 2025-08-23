# CTA LineWatch (CodeIgniter 4)

This repository hosts CTA LineWatch — an unofficial Chicago CTA 'L' arrivals and lines API — built on CodeIgniter 4. The legacy PHP site has been archived into `legacy-site/`, and the CodeIgniter app now lives at the repository root.

See `MIGRATION.md` for details of the restructure.

## Layout
```
app/
public/
vendor/
writable/
tests/
composer.json
composer.lock
spark
preload.php
phpunit.xml.dist
LICENSE
.env (or env)
legacy-site/   # archived legacy site
```

## Quick start
1) Dependencies: `composer install`
2) Environment: copy `env` to `.env` and set `app.baseURL` and any DB settings
3) Run locally: `php spark serve -port 8080` then visit http://localhost:8080

### Configure CTA Train Tracker
- Obtain an API key from CTA (Train Tracker): https://www.transitchicago.com/developers/ttdocs/
- In your `.env` set:
	- `CTA_TRAIN_API_KEY=your_key_here`
	- Optionally override the base endpoint (defaults to CTA’s): `CTA_TRAIN_API_BASE=https://lapi.transitchicago.com/api/1.0/ttarrivals.aspx`
  
Your key is read by `App\Libraries\CtaTrainTracker` and is never hardcoded.

## Deploy
Point your web server DocumentRoot to the `public/` directory at the repository root. Do not point the server at the repo root.

### Apache (vhost excerpt)
```
DocumentRoot /var/www/cta-linewatch/public
<Directory /var/www/cta-linewatch/public>
	AllowOverride All
	Require all granted
</Directory>
```

### Nginx (server block excerpt)
```
root /var/www/cta-linewatch/public;
index index.php index.html;
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; fastcgi_pass php-fpm; }
```

## Docker

Quick local run (Apache + PHP 8.2):

```bash
docker compose up --build -d
```

Then open http://localhost:8080

Environment variables:
- `CTA_TRAIN_API_KEY` (required) – your CTA Train Tracker key
- `CTA_TRAIN_API_BASE` (optional) – override upstream arrivals endpoint

To pass your key on startup:

```bash
CTA_TRAIN_API_KEY=your_key_here docker compose up --build -d
```

Production notes:
- Provide secrets via your platform’s secret store (do not bake keys into images).
- Place a reverse proxy (e.g., Cloudflare, Nginx) in front as needed.
- Ensure `writable/` is persisted and owned by the web user.

## Requirements
PHP 8.1+ with intl and mbstring extensions. See CodeIgniter 4 user guide for details.

## Notes
- Legacy files remain in `legacy-site/` for reference. Consider server-level 301 redirects from old routes if needed.
- Tests: `vendor/bin/phpunit`
