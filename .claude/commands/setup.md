---
description: Run the install steps to set up the note-app locally (SQLite)
---

Set up this Laravel note-app for local development. Run each step in order and stop if any step fails.

1. Run `composer update` to install PHP dependencies. (The committed `composer.lock` pins packages that don't support recent PHP versions, so `composer install` will often fail — use `composer update` to resolve against the current PHP runtime.)
2. If `.env` does not exist, copy `.env.example` to `.env` (`cp .env.example .env`). If `.env` already exists, leave it alone.
3. If `database/database.sqlite` does not exist, create it with `touch database/database.sqlite`.
4. Run `php artisan key:generate`.
5. Run `php artisan storage:link` (skip if the symlink already exists).
6. Run `php artisan migrate`. The app uses SQLite — no DB credentials needed.
7. Create a default dev user if one doesn't already exist. Run:
   `php artisan tinker --execute="App\Models\User::firstOrCreate(['email' => 'dev@localhost'], ['name' => 'Dev', 'password' => bcrypt('password')]);"`
8. If `package.json` exists and `node_modules` is missing, run `npm install` and then `npm run dev` to build frontend assets.
9. Tell the user setup is complete and that they can start the dev server with `php artisan serve` and visit `http://localhost:8000/`. Mention that they can sign in with email `dev@localhost` and password `password`. Do not start the server yourself unless the user asks.
