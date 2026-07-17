# DestinyCommand

Legacy Destiny chatbot API and utility site, upgraded to Laravel 13.

This repository is a modernization of the archived `xgerhard/DestinyCommand` project. The current goal is to preserve the custom PHP application behavior while moving it onto a maintainable Laravel 13 codebase.

## Getting Started

### Requirements

- PHP 8.3+
- Composer 2+
- A database supported by Laravel

Node is optional. The app's important behavior is PHP-first and server-rendered.

### Install

1. Clone the repository.
2. Install dependencies with `composer install`.
3. Create your environment file with `cp .env.example .env`.
4. Generate an app key with `php artisan key:generate`.
5. Configure your database and application URL in `.env`.
6. Run migrations with `php artisan migrate`.

### Required Environment Variables

- `APP_URL`: public base URL for the app
- `BUNGIE_API_KEY`: Bungie API key used for Destiny API requests
- `MODERATOR_KEYS`: optional semicolon-separated moderator hashes for `setxur`
- `DESTINYCOMMAND_REQUEST_ORIGIN`: optional override for the outbound Bungie `Origin` header

### OAuth Provider Setup

The legacy OAuth flow expects rows in the `oauth_providers` table for at least:

- `Bungie`
- `Nightbot`

Each row should include values for:

- `name`
- `auth_url`
- `token_url`
- `client_id`
- `client_secret`
- `scope` when required
- `redirect_url`
- `local_redirect`

### Running Locally

- Start the app with `php artisan serve`
- Run tests with `php artisan test`
- Format/lint PHP code with `./vendor/bin/pint`

### Deployment Note

Serve the application from the `public/` directory, as with a standard Laravel app.

## Project Layout

- `app/`: application code, including the preserved legacy Destiny, OAuth, and command logic
- `config/`: Laravel and project-specific configuration
- `database/migrations/`: Laravel 13 schema, including inferred legacy support tables
- `resources/views/`: Blade templates for the landing page and tools

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This fork retains the upstream MIT licensing position from the original project metadata. See [LICENSE](LICENSE).
