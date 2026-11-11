# Contributing and training exercises

Start with the [setup guide](docs/00_SETUP_AND_INSTALLATION.md) and use your own
local training database. All seeded names and email addresses are sample data.

1. Fork the repository and clone your fork if you plan to submit changes.
2. Create a branch: `git switch -c lesson/describe-your-change`.
3. Keep each change focused. Follow the existing Laravel structure and use four
   spaces for PHP indentation.
4. Run `composer validate --strict` and `php artisan test`.
5. Review `git diff` and `git status` before committing. Keep `.env`, credentials,
   database files, logs, and `vendor/` out of commits.
6. Open a pull request explaining the change and how you checked it.

Good first exercises: add a task filter, improve validation messages, add a Blade
page test, or explain a relationship in the lessons. Add a feature test when an
exercise changes application behavior.

Tests use SQLite in memory and refuse other database connections. Do not point
tests at a shared database. MariaDB behavior should be checked separately using
a disposable training database.

This app has no authentication and is intended for local training. Report code
bugs with reproduction steps and expected behavior. Do not include private data,
credentials, or sensitive vulnerability details in public issues.
