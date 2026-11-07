# Microblog

Microblog is a small social networking app built with Laravel and Vue. Users can register and verify their email, sign in, publish and manage posts, like posts, search for people, view profiles, and update their account details and avatar.

This used to be a capstone project on one of my previous companies to showcase my capabilities on modern JavaScript and Laravel. It started as a PHP 7.4 + Laravel 8 backend with a Vue 2 + Vuetify + Material Design Icons + Webpack frontend. I have since updated the project dependencies to use PHP 8.0 + Laravel 9 and Vue 3 + TailwindCSS + Lucide + Vite. Currently, it is using PHP 8.2 + Laravel 12 and Vue 3 + TailwindCSS + Lucide + Vite and using Amazon S3 for image uploads.

## Requirements

- Docker and Docker Compose
- PHP 8.2 or newer (or use the PHP 8.2 Sail container for Composer commands)
- Composer
- Git

## Configure and run

1. Install the PHP dependencies and create your local environment file:

   ```sh
   composer install
   cp .env.example .env
   ```

2. Edit `.env` and configure the application and Docker Compose settings. For the default local setup, use values like:

   ```dotenv
   APP_NAME=Microblog
   APP_URL=http://localhost
   APP_TIMEZONE=Asia/Manila

   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=microblog
   DB_USERNAME=sail
   DB_PASSWORD=password
   ```

3. Start Docker, build and start the containers, then generate the application key and create the database tables:

   ```sh
   ./vendor/bin/sail up -d --build
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ./vendor/bin/sail artisan storage:link
   ```

   Seeding is optional; it creates sample content and a default admin account (`admin` / `password`). Do not use the seeded credentials outside local development.

   Uploaded avatars and post images use the public filesystem disk by default. To use S3, configure `FILESYSTEM_DISK=s3` and the AWS settings in `.env`.
   Email verification and password recovery emails are sent through the configured mail transport. The local Docker setup includes Mailpit; view captured messages at [http://localhost:8025](http://localhost:8025). Configure `MAIL_*` in `.env` for a production mail provider.

4. Install the JavaScript dependencies and build the frontend:

   ```sh
   ./vendor/bin/sail npm ci
   ./vendor/bin/sail npm run build
   ```

5. Open [http://localhost](http://localhost) in your browser. The app container serves the site on port 80 by default; set `APP_PORT` in `.env` if that port is already in use.

## Frontend development

To run Vite in development mode with hot reloading, start the app containers and run this in a separate terminal:

```sh
./vendor/bin/sail npm run dev
```

The Vite development server uses port 5173 by default (`VITE_PORT` in `.env`). Stop it with `Ctrl+C`; use `npm run build` again when you want to produce production assets.

## Stopping the app

```sh
./vendor/bin/sail down
```

The MySQL data is stored in a Docker volume and is retained when the containers are stopped.
