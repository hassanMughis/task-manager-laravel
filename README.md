# Task Manager (Laravel Practice Project)

A small Laravel app for practicing Git/GitHub, CI/CD with GitHub Actions, and
deployment to Render's native PHP environment (with a path towards AWS
later). It is intentionally plain — the goal is to learn the workflow around
a Laravel app, not the UI.

## Features

- Register / login / logout (Laravel's built-in session auth)
- Create, view, edit, delete tasks
- Mark tasks as completed / pending
- Each user only sees and can manage their **own** tasks
- Dashboard listing your tasks with pagination

## Tech stack

- Laravel 10 (PHP), Blade templates, Eloquent ORM
- PostgreSQL (recommended for local dev and required in production — see below)
- PHPUnit for feature tests
- GitHub Actions for CI
- Render (native PHP environment) for deployment

---

## 1. Installation

**Requirements:** PHP 8.1+, Composer, and a PostgreSQL (or MySQL) server.

```bash
git clone <your-repo-url> task-manager
cd task-manager
composer install
cp .env.example .env
php artisan key:generate
```

`composer install` downloads Laravel and its dependencies into `vendor/`
(this folder is git-ignored — never commit it).
`php artisan key:generate` fills in `APP_KEY` in your `.env`, which Laravel
uses to encrypt sessions and cookies.

## 2. Configure `.env`

Open the `.env` file that was created from `.env.example` and set your
database connection:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

`.env` is never committed to git (see `.gitignore`) — it holds secrets and is
different for every environment (local machine, CI, production). `.env.example`
is the committed template that documents which variables exist.

## 3. Create the database

Create an empty database matching `DB_DATABASE` above, e.g. with `psql`:

```bash
psql -U postgres -c "CREATE DATABASE task_manager;"
```

(If you'd rather use MySQL locally, set `DB_CONNECTION=mysql` and the matching
`DB_HOST`/`DB_PORT` in `.env`, then create the database with your MySQL client.)

## 4. Run migrations

```bash
php artisan migrate
```

This creates the `users`, `password_reset_tokens`, `failed_jobs`, and `tasks`
tables. Optionally seed a demo user with a few example tasks:

```bash
php artisan db:seed
# Demo login: demo@example.com / password
```

## 5. Run the application

```bash
php artisan serve
```

Visit `http://localhost:8000`, register an account, and start creating tasks.

## 6. Run tests

```bash
php artisan test
```

Tests run against an **in-memory SQLite database** (configured in
`phpunit.xml`), so they never touch your real Postgres/MySQL data. This is
the same command the GitHub Actions workflow runs on every push.

---

## Project structure

```
app/
  Http/Controllers/        Auth/, Task, Dashboard controllers
  Http/Middleware/         Auth guards (login-required, guest-only, etc.)
  Models/                  User, Task (Eloquent models + relationship)
  Policies/                TaskPolicy — a user may only touch their own tasks
  Providers/                Service providers (routes, auth, app boot logic)
database/
  migrations/               Schema for users + tasks tables
  factories/, seeders/       Test data / demo data generators
resources/views/
  layouts/app.blade.php     Shared page layout (nav, flash messages, styles)
  auth/                      Login & register forms
  dashboard.blade.php        Task list
  tasks/                     Create/edit forms
routes/
  web.php                    All application routes
tests/
  Feature/Auth/               Registration & login tests
  Feature/TaskTest.php        Task CRUD + authorization tests
.github/workflows/tests.yml   CI: runs the test suite on every push/PR
```

### How the pieces fit together (MVC)

1. A request hits a **route** in `routes/web.php`.
2. Routes are protected by **middleware** (`auth` requires login, `guest`
   blocks logged-in users from seeing the login/register pages again).
3. The route calls a **controller** method (e.g. `TaskController@store`),
   which validates input and talks to the **model** (`Task`, via Eloquent).
4. Sensitive actions (`edit`, `update`, `delete`) additionally check a
   **policy** (`TaskPolicy`) so a user can never touch someone else's task,
   even if they guess the URL.
5. The controller returns a **Blade view** which renders HTML using the data
   it was given.

---

## Continuous Integration (GitHub Actions)

`.github/workflows/tests.yml` runs automatically on every `git push` and on
every pull request. It:

1. Checks out the code.
2. Installs PHP 8.2 with the extensions Laravel needs.
3. Runs `composer install`.
4. Copies `.env.example` to `.env` and generates an app key.
5. Runs `php artisan test`.

If a test fails, the workflow fails and shows up as a red ❌ on your commit /
pull request in GitHub — this is the core CI/CD habit worth practicing.

---

## Deploying to Render

This project deploys to Render as a **native PHP web service** — no
Dockerfile or container involved. Render simply runs your build command
once per deploy, then keeps your start command running to serve traffic.

### 1. Connect the GitHub repository

- Push this project to GitHub (see the Git/GitHub practice section below if
  you're new to this).
- In the Render dashboard: **New** → **Web Service**.
- Connect your GitHub account if you haven't already, then select this repo.
- For **Language/Runtime**, choose **PHP**.

### 2. Create a PostgreSQL database on Render

- Render dashboard → **New** → **PostgreSQL**.
- Once it's created, copy the **Internal Database URL** — you'll need it below.

### 3. Build Command

```
composer install --no-dev --optimize-autoloader
```

This installs Laravel and its dependencies (skipping dev-only packages like
PHPUnit, since those aren't needed to run the app in production).

### 4. Start Command

```
php artisan serve --host 0.0.0.0 --port $PORT
```

`--host 0.0.0.0` lets Render's proxy reach the app, and `$PORT` is the port
Render assigns your service at runtime (Render sets this automatically as
an environment variable — you don't need to set it yourself).

### 5. Required environment variables

Set these on the Web Service (Settings → Environment):

| Variable        | Value                                                                 |
|------------------|------------------------------------------------------------------------|
| `APP_NAME`       | `Task Manager`                                                        |
| `APP_ENV`        | `production`                                                          |
| `APP_DEBUG`      | `false`                                                                |
| `APP_URL`        | Your Render URL, e.g. `https://task-manager.onrender.com`             |
| `APP_KEY`        | Output of running `php artisan key:generate --show` locally           |
| `DB_CONNECTION`  | `pgsql`                                                                |
| `DATABASE_URL`   | The **Internal Database URL** from the Postgres instance you created  |
| `LOG_CHANNEL`    | `stderr` (so logs show up in Render's log viewer)                     |

Laravel reads `DATABASE_URL` automatically to fill in host/port/database/
credentials for the `pgsql` connection (see `config/database.php`), so you
don't need to set `DB_HOST`, `DB_USERNAME`, etc. separately on Render.
`APP_KEY`, database credentials, `APP_ENV`, `APP_DEBUG`, and `APP_URL` all
come from environment variables here — nothing is hardcoded in the app.

### 6. Run migrations

The build/start commands above only install dependencies and serve the app —
they don't touch the database. Run migrations once after your first deploy
(and again after any future migration changes) using Render's **Shell** tab
on the Web Service:

```bash
php artisan migrate --force
```

(`--force` is required because `APP_ENV=production` normally asks for
confirmation before running migrations.)

### 7. Deploy and verify

Click **Create Web Service** (or **Manual Deploy** if it already exists).
Once the build finishes and the app is live, visit your Render URL, register
an account, and confirm you can create, edit, complete, and delete tasks.

---

## Practicing Git & GitHub with this project

Once the app runs locally:

```bash
git init
git add .
git commit -m "Initial commit: Task Manager Laravel app"
git branch -M main
git remote add origin <your-empty-github-repo-url>
git push -u origin main
```

From here, good habits to practice:

- Create a feature branch for each change (`git checkout -b add-task-priority`).
- Open a pull request and watch the GitHub Actions test workflow run.
- Merge to `main` only once tests pass.
- Every push to `main` (once connected to Render) can trigger an automatic
  redeploy — Render watches your GitHub repo for new commits.

## Next steps towards AWS

Once you're comfortable with this Render deployment flow, the same
environment-variable based configuration (`DATABASE_URL`, `APP_KEY`,
`APP_ENV`, etc.) carries over to AWS with little change — for example,
running the app on **Elastic Beanstalk** (which also has a native PHP
platform) with an **RDS PostgreSQL** database in place of Render's managed
Postgres. If you later want a containerized deployment, that's also when
introducing Docker would make sense — but it isn't needed for this project.
