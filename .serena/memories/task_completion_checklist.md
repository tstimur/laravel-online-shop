# Что делать после изменений

1. Проверить форматирование PHP:
- `vendor/bin/php-cs-fixer fix --dry-run`

2. При необходимости исправить формат:
- `vendor/bin/php-cs-fixer fix`

3. Прогнать тесты:
- `composer test`

4. Если затронуты миграции/данные:
- `php artisan migrate`
- при необходимости `php artisan db:seed`

5. Если затронута работа со статикой/фронтендом:
- `npm run build` (или `npm run dev` для локальной проверки)

6. Если затронуты загрузки в `storage`:
- убедиться, что есть `php artisan storage:link`.

7. Проверить ключевые пользовательские сценарии вручную:
- каталог/фильтры, корзина, профиль/адреса, заказы;
- если изменения в админке: разделы `admin/roles`, `admin/users`, `admin/products`, `admin/orders`.