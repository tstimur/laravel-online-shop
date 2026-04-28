# Полезные команды

## Основные (из проекта)
- `make init` — первичная инициализация окружения (контейнеры, зависимости, запуск, вход в php-fpm).
- `make up` / `make down` — поднять/остановить docker-стек.
- `composer setup` — install deps, `.env`, key, migrate, frontend build.
- `composer dev` — локальный набор процессов (`artisan serve`, queue, logs, vite).
- `npm run dev` — только Vite dev server.
- `npm run build` — production frontend build.
- `composer test` — тесты через `php artisan test`.

## Качество кода
- `vendor/bin/php-cs-fixer fix --dry-run` — проверить форматирование.
- `vendor/bin/php-cs-fixer fix` — применить форматирование.

## Laravel utility
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan storage:link`
- `php artisan config:clear`

## Docker utility из Makefile
- `make console` — shell в контейнере `php-fpm`.
- `make build`, `make stop`, `make start`, `make restart`.

## Базовые системные команды (Darwin/macOS)
- `ls`, `cd`, `pwd`
- `find`, `grep`, `rg`
- `git status`, `git diff`, `git log`