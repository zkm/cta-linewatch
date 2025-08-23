# CTA API (CodeIgniter 4)

This repository hosts the CTA API application built on CodeIgniter 4. The legacy PHP site has been archived into `legacy-site/`, and the CodeIgniter app now lives at the repository root.

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

## Deploy
Point your web server DocumentRoot to the `public/` directory at the repository root. Do not point the server at the repo root.

### Apache (vhost excerpt)
```
DocumentRoot /var/www/cta-api/public
<Directory /var/www/cta-api/public>
	AllowOverride All
	Require all granted
</Directory>
```

### Nginx (server block excerpt)
```
root /var/www/cta-api/public;
index index.php index.html;
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; fastcgi_pass php-fpm; }
```

## Requirements
PHP 8.1+ with intl and mbstring extensions. See CodeIgniter 4 user guide for details.

## Notes
- Legacy files remain in `legacy-site/` for reference. Consider server-level 301 redirects from old routes if needed.
- Tests: `vendor/bin/phpunit`
