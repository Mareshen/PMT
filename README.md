# PMT - PixelMindTrainning

A Laravel training project for teaching relational database design, Eloquent relationships, JSON APIs, and server-rendered MVC. Students, categories, tasks, and assignments provide a small domain to explore and extend.

## Quick start

Requirements: **PHP 8.4.1+**, Composer 2, and PDO SQLite. Enable the PHP extensions required by Composer (including mbstring, DOM, XML, and XMLWriter for tests). The committed dependency lock file requires PHP 8.4.1 or later. No Node.js or frontend build is needed.

Clone this repository from its GitHub page, open a terminal in the cloned directory, then run:

```sh
composer install
```

Copy the local environment template:

```powershell
# Windows PowerShell
Copy-Item .env.example .env
```

```sh
# macOS / Linux
cp .env.example .env
```

Create the SQLite database and seed the sample lessons:

```sh
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Use a new database for training. The example environment defaults to SQLite; [MariaDB setup and troubleshooting](docs/00_SETUP_AND_INSTALLATION.md) are also documented. Keep your `.env` private. The seeder creates four students, five categories, and six tasks; it skips populated training tables to preserve edits.

| URL | Purpose |
| --- | --- |
| http://127.0.0.1:8000/tasks | Blade pages for managing tasks and assignments |
| http://127.0.0.1:8000/api/tasks | JSON API with filters and pagination |
| http://127.0.0.1:8000/docs/api | Interactive API documentation (local environment) |
| http://127.0.0.1:8000/docs/api.json | OpenAPI contract |

This is an unauthenticated local teaching application. Keep the development server bound to localhost and use sample data.

## Project map

| Directory | Contents |
| --- | --- |
| `app/Models` | Students, categories, tasks, and Eloquent relationships |
| `app/Http/Controllers` | JSON API actions and Blade page actions |
| `routes` | API and web routes |
| `database` | Migration and repeatable sample-data seeder |
| `resources/views` | Blade layouts and task pages |
| `tests/Feature` | API, database constraints, Blade, and OpenAPI checks |
| `docs` | Setup, lessons, teaching guide, and verification notes |

Tasks belong to one category and have many assigned students through `student_task`. Task status is shared across assigned students. Deleting a student or task removes its assignments; a category with tasks cannot be deleted.

## Checks and contribution

```sh
composer validate --strict
php artisan test
```

Tests force SQLite in memory and refuse other database connections. Clear cached configuration with `php artisan config:clear` if you previously cached it. GitHub Actions runs the suite on PHP 8.4 and 8.5. See [verification notes](docs/VERIFICATION.md) for checked behavior and remaining integration limits, and [CONTRIBUTING.md](CONTRIBUTING.md) for exercises and the pull request workflow.

## Lessons

1. [Setup and installation](docs/00_SETUP_AND_INSTALLATION.md)
2. [Requirements and database](docs/01_REQUIREMENTS_AND_DATABASE.md)
3. [SQL to Eloquent](docs/02_SQL_TO_ELOQUENT.md)
4. [PHP and Laravel syntax](docs/03_PHP_AND_LARAVEL_SYNTAX.md)
5. [MVC and request lifecycle](docs/04_MVC_AND_REQUEST_LIFECYCLE.md)
6. [Laravel operations](docs/05_LARAVEL_OPERATIONS.md)
7. [API and Swagger/OpenAPI](docs/06_API_AND_SWAGGER.md)
8. [Teaching guide](docs/07_TEACHING_GUIDE.md)
9. [Git and publishing](docs/GIT_LESSON.md)
10. [Database, ERDs, and Eloquent teaching guide](docs/DATABASE_AND_ELOQUENT_TEACHING_GUIDE.md)
