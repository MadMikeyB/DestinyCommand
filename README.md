# DestinyCommand

Destiny Command is an app/command you can add to your chat bot that allows you and your viewers to check their stats across Destiny 2 as a whole. Whether it be Trials stats, K/D, loadout or just checking the amount of times you've achieved a certain medal.

We support all platforms, but mainly focus on Twitch. There are bots supporting the command on Twitch, Youtube, Discord and Slack.

Installation is simple, choose your bot below and follow the instructions. For most bots a simple copy-paste in your chat is enough!

## Credits

This repository is a modernization of the archived `xgerhard/DestinyCommand` project. Thank you to xgerhard for tirelessly maintaining destinycommand.com over the years. Consider donating: https://paypal.me/xgerhard. 

## Getting Started

### Requirements

- PHP 8.5+
- Composer 2+
- A database supported by Laravel

## Local Set Up Instructions

Follow these steps to set up the Laravel/Inertia.js application locally:

### 1. Clone the Repository
```bash
git clone https://github.com/MadMikeyB/DestinyCommand.git
cd DestinyCommand
```

### 2. Set Up a Local PHP Development Environment
You can use [php.new](https://php.new) to quickly spin up a local PHP environment or set up one manually:

- **Using php.new**:
    1. Visit [php.new](https://php.new) and follow the instructions to set up a local PHP environment.
    2. Ensure you have Composer installed globally.
    3. Install a local database server (MySQL, PostgreSQL, or use [SQLite](https://laravel.com/docs/master/database#sqlite-configuration)).
        - You can use [DBNgin](https://dbngin.com/) to get a database server set up (I have only tested this on macOS)
    4. Ensure Redis is installed (Pretty sure DBNgin can do this too.)

- **Manual Setup**:
    1. Install PHP (version 8.5 or higher) and Composer.
    2. Install a database server (e.g., MySQL or MariaDB).
    3. Set up a web server (e.g., Apache or Nginx).
    4. Ensure Redis is installed.

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Set Up Environment Variables
1. Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
2. Update the `.env` file with your local database credentials and other required configurations.

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Set Up the Database
1. Create a new database for the application.
2. Run migrations and seeders:
```bash
php artisan migrate --seed
```

### 7. Obtain a Bungie API Key
1. Visit the [Bungie Developer Portal](https://www.bungie.net/en/Application).
2. Log in with your Bungie account and create a new application.
3. Note down the API key and client secret.
4. Update the API key and secret in your `.env` file:
```env
BUNGIE_API_KEY=your_api_key
BUNGIE_CLIENT_ID=your_client_id
BUNGIE_CLIENT_SECRET=your_client_secret
```
### 8. Start the Development Server
```bash
composer run dev
```
Visit `https://localhost:8000` in your browser to access the application.

> [!NOTE]
> `composer run dev` will do the following for you in a terminal window: 
>  - Start the php development server via `php artisan serve`
>  - Start the queue listeners via `php artisan queue:listen`
>  - Start Pail (Laravel's "tail" for all app log files) via `php artisan pail`
>  - Run `npm run dev` to run the front end server.
r the landing page and tools

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This fork retains the upstream MIT licensing position from the original project metadata. See [LICENSE](LICENSE).
