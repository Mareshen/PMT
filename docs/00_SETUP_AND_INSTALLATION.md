# Setup and installation

## Requirements

- PHP **8.4.1 or later** for the committed dependency versions.
- Composer 2 and Git.
- PDO SQLite for the quick start and isolated tests, or PDO MySQL for MariaDB.
- PHP extensions required by Composer, including mbstring, DOM, XML, XMLWriter, OpenSSL, and fileinfo. Run `composer check-platform-reqs` after installation.

The app uses Laravel 13, Scramble for OpenAPI, and PHPUnit 12. There is no Node.js build step. XAMPP is optional; check its PHP version before using it. Composer installs the versions in `composer.lock`; use `composer install`, not `composer update`, when joining the project.

## SQLite quick start

Clone the repository, enter its directory, and run `composer install`.
Copy `.env.example` to `.env` with `Copy-Item .env.example .env` in PowerShell or `cp .env.example .env` on macOS/Linux. Do not overwrite an existing local `.env`.

```sh
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Keep that terminal running. Open http://127.0.0.1:8000/tasks. SQLite stores local data in `database/database.sqlite`, which Git ignores. The example config uses an absolute project path internally, so no machine-specific path is required. Keep `DB_DATABASE` unset for SQLite, or set it to your own absolute file path.

The seed supplies fictional lesson data only when students, categories, and tasks are all empty. Running it again preserves edits. Fixed due dates make the lesson data repeatable; some examples may be overdue.

## MariaDB option

Use a new local database dedicated to this project. For example, create `pmt_training` with the `utf8mb4_unicode_ci` collation using your database admin tool, then configure a local user with access to it. In `.env`, set:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pmt_training
DB_USERNAME=pmt_training
DB_PASSWORD=your-local-password
```

Replace the sample username and password with your own local credentials. Then run:

```sh
php artisan config:clear
php artisan migrate --pretend
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

For an existing database, inspect its tables and back it up before migrating. Do not run `migrate:fresh`, `migrate:reset`, or rollback commands against data you need. Migration creates the domain tables and Laravel's migration history table; it does not reconcile an existing conflicting schema.

## Test and explore

```sh
composer validate --strict
composer check-platform-reqs
php artisan config:clear
php artisan test
php artisan route:list
```

Tests force SQLite `:memory:` and check the connection before running migrations. They do not require MariaDB or a populated local database. SQLite checks do not verify MariaDB's engine-specific locking or enum behavior.

Visit `/api/tasks`, `/docs/api`, and `/docs/api.json` on the running server. API routes are registered in `bootstrap/app.php`, with Laravel's automatic `/api` prefix. Scramble's documentation routes are available in the local environment. For API requests, send `Accept: application/json`; see the [API lesson](06_API_AND_SWAGGER.md) for executable examples.

## Troubleshooting

- Composer rejects the PHP version: check `php -v` and which executable is on PATH. The lock file requires PHP 8.4.1+, even though Laravel itself allows 8.3.
- `could not find driver`: enable `pdo_sqlite` or `pdo_mysql` in the CLI PHP configuration shown by `php --ini`.
- SQLite file missing: run the file creation command above before migrating.
- `No application encryption key`: run `php artisan key:generate` after creating `.env`.
- Database configuration seems stale: run `php artisan config:clear`.
- Storage permission error: ensure `storage` and `bootstrap/cache` are writable by your local PHP process.
- API docs return 403: use `APP_ENV=local`, then clear cached configuration.

`APP_DEBUG=false` is the default. For local debugging, inspect `storage/logs/laravel.log` or temporarily enable debugging in your private `.env`.
