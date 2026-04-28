# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 12 application for an online shop. Core backend code lives in `app/`:
`Http/Controllers` for request flow, `Http/Requests` for validation, `Models` for Eloquent entities, `Service` for business logic, and `DTO` for typed payloads. Routes are defined in `routes/web.php`. Blade templates and frontend entry points live in `resources/views`, `resources/css`, and `resources/js`. Database schema, factories, and seeders are in `database/`. Automated tests are split into `tests/Feature` and `tests/Unit`. Container setup is under `docker/`, with orchestration in `docker-compose.yml`.

## Build, Test, and Development Commands
Use the commands already defined by the project:

- `make init` builds containers, installs Composer dependencies, starts services, and opens a shell in `php-fpm`.
- `make up` / `make down` start or stop the Docker stack.
- `composer setup` installs PHP and Node dependencies, creates `.env`, generates the app key, runs migrations, and builds assets.
- `composer dev` runs the Laravel server, queue listener, log tailing, and Vite in one process group.
- `npm run dev` starts Vite only; `npm run build` creates production assets.
- `composer test` runs the PHPUnit suite through `php artisan test`.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF line endings, spaces, and 4-space indentation for PHP. PHP code should stay PSR-12 compliant and keep `declare(strict_types=1);` where applicable; check formatting with `vendor/bin/php-cs-fixer fix --dry-run` and apply fixes with `vendor/bin/php-cs-fixer fix`. Use PSR-4 class names such as `ProductService`, `OrderStoreRequest`, and `RegisterDto`. Blade partials use lowercase snake case such as `resources/views/cart/_content.blade.php`.

## Testing Guidelines
Tests use PHPUnit 11. Place HTTP and integration coverage in `tests/Feature`; keep isolated logic in `tests/Unit`. Name files with the `*Test.php` suffix and write descriptive test methods around observable behavior. The test config uses in-memory SQLite, so new tests should not depend on the local PostgreSQL container unless explicitly required.

## Commit & Pull Request Guidelines
Recent history follows short conventional prefixes like `feat:`, `fix:`, `style:`, and `Revert`. Keep commit messages imperative and scoped, for example `feat: add order status validation`. Pull requests should describe the user-facing change, list migrations or env changes, link the related task, and include screenshots for Blade/UI updates.

## Security & Configuration Tips
Start from `.env.example`. Local Docker defaults to PostgreSQL (`DB_HOST=db`, `DB_PORT=5432`) and serves the app on `PROJECT_PORT`. Do not commit real secrets, generated `.env` values, or database dumps.

## Требования к задаче:
1. Не редактировать глобальные файлы. Редактировать только указанные файлы в @AGENTS.md или в прямом сообщении тебе.
2. 
3. При выполнении задачи ты показываешь подробный план решения с расширенной аргументацией решения. Затем после ознакомления мной, я даю подтверждение на редактирование.
4. При каждом моем запросе, ты всегда проверяешь содержимое @AGENTS.md.
5. При каждом моем запросе, ты всегда анализируешь самого начала файлы указанные в задании в @AGENTS.md.
6. Любые твои комментарии должны быть на русском языке


## Исключения при выполнении задачи:
1. Если необходимо редактирование файла не указанного в зададнии, то сначала ты также готовишь решение, аргументируешь, а затем запрашиваешь мое подтверждение



### Формулировка задачи:


### Комментарий к задаче:
Также прочитай все что лежит в docs

### Файл, которые нужно проанализировать:


#### Пример:
```

```
