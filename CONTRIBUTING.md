# Contributing

## Workflow

1. Create a feature branch from the current development branch.
2. Keep changes focused and minimal.
3. Preserve existing chatbot and route behavior unless a change is intentional and documented.

## Local Checks

Run these before opening a PR:

- `./vendor/bin/pint`
- `php artisan test`
- `php artisan route:list`

If you touch database behavior, also run:

- `php artisan migrate:fresh --force`

## Laravel 13 Conventions

- Do not call `env()` directly from application code.
- Read runtime settings through `config()`.
- Keep framework wiring in Laravel 13 locations such as `bootstrap/app.php` and `bootstrap/providers.php`.
- Prefer small, explicit migrations over hidden schema assumptions.

## Legacy Code Notes

- Much of the Destiny command parsing and provider logic is preserved from the original application.
- When modernizing code, prefer behavior-preserving refactors.
- If you remove dead code or obsolete dependencies, note that clearly in the change summary.
