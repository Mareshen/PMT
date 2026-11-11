# Verification notes

Verified on **11 November 2026**, using PHP 8.5.12 on Windows.

Installed versions: Laravel **13.40.0**, Scramble **0.13.53**, and PHPUnit **12.5.43**.
The committed lock file requires PHP **8.4.1+**.

| Check | Result |
| --- | --- |
| Composer manifest and lock validation | Passed |
| Composer platform requirements | Passed |
| Composer locked dependency security audit | No vulnerability advisories found on the verification date |
| PHP syntax | Passed for application PHP files and entry points |
| Feature suite | **12 tests passed, 136 assertions** |
| Isolated SQLite setup from the example environment | Key generation, migration, and seeding passed |
| Seed counts in the isolated copy | 4 students, 5 categories, 6 tasks |
| Blade list/create/detail/edit pages | Returned HTTP 200 through Laravel's HTTP kernel |
| Tasks, students, and categories API lists | Returned HTTP 200 through Laravel's HTTP kernel |
| Documentation UI and JSON routes | Returned HTTP 200 |
| Scramble export | OpenAPI 3.1.2, 11 paths |
| Relative Markdown links | Resolved |
| Publishable file review | No common credential patterns found; private environment, dependencies, local databases, runtime files, and Composer binary excluded |

The setup check used a separate copy of the publishable source and existing
installed dependencies, with regenerated Composer autoload files and package
discovery. It did not redownload every package from an empty Composer cache.
Only the isolated copy's SQLite database was migrated and seeded; no migration
or seeding commands were run against the existing local MariaDB database.

## Scope and remaining checks

- GitHub Actions is configured for PHP 8.4 and 8.5; hosted execution will be
  verified after pushing. Local execution used PHP 8.5.12.
- MariaDB-specific engine, enum, and concurrent locking behavior have not been
  integration-tested in this preparation pass. SQLite covers application
  behavior and the constraint cases in the feature suite.
- Browser interaction and a complete review of inferred OpenAPI response
  schemas remain manual checks. Kernel requests verified route responses.
- The credential scan checks common patterns and the publication file list;
  it is not a guarantee against every possible secret format.

Repeat the checks after changing dependencies or application behavior.
