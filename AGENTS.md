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
```css
Базовая архитектура

  Для учебного проекта здесь подходит простая layered-архитектура:

  - Controllers принимают HTTP-запрос, вызывают сервис, возвращают Blade/View или redirect.
  - FormRequest валидирует входные данные.
  - DTO передаёт уже подготовленные данные в сервис.
  - Service содержит бизнес-логику.
  - Model описывает таблицы и связи Eloquent.

  Это хорошо ложится на текущую структуру проекта, где уже есть app/Http/Controllers, app/Http/Requests, app/DTO, app/Service.

  Сущности и таблицы

  Основные таблицы:

  - users: id, first_name, last_name, email, phone, password, avatar, status, timestamps.
  - roles: id, name, slug, timestamps.
  - role_user: user_id, role_id. Уникальная пара user_id + role_id.
  - products: id, name, description, price, image, stock, status, sku, timestamps.
  - orders: id, user_id, address_id, total, status, shipping_address, timestamps.
  - order_items: id, order_id, product_id, quantity, price, timestamps`.
  - Дополнительно уже уместна таблица addresses, она в проекте есть и её лучше использовать для доставки.

  Статусы:

  - users.status: active, blocked
  - products.status: active, inactive
  - orders.status: pending, paid, shipped, completed, canceled

  Связи между сущностями

  - User belongsToMany Role
  - Role belongsToMany User
  - User hasMany Order
  - User hasMany Address
  - Order belongsTo User
  - Order belongsTo Address
  - Order hasMany OrderItem
  - OrderItem belongsTo Order
  - OrderItem belongsTo Product
  - Product hasMany OrderItem

  Система ролей

  Без внешних библиотек, полностью своя реализация:

  - Модель Role
  - Pivot-таблица role_user
  - Методы в User: roles(), hasRole(string $role): bool, hasAnyRole(array $roles): bool
  - Middleware CheckRoleMiddleware, например role:admin или role:admin,manager
  - Seeder с ролями:
      - user
      - admin
      - manager

  Рекомендованная матрица доступа:

  - admin: полный доступ ко всем разделам админки
  - manager: dashboard, товары, заказы
  - user: только клиентская часть сайта

  Важно: в требованиях есть противоречие. С одной стороны нужна роль manager, с другой написано, что без роли администратора вход в админку запрещён. Для учебного проекта логичнее сделать доступ в админку для admin и manager, а раздел ролей и пользователей оставить только admin.

  Ограничения доступа

  Маршруты стоит разделить на 3 группы:

  - публичные: каталог, карточка товара, регистрация, вход
  - авторизованные пользователи: профиль, адреса, свои заказы, корзина
  - административные: /admin/*

  Защита админки:

  - middleware auth
  - middleware role:admin,manager для панели
  - middleware role:admin для ролей и управления пользователями

  Если пользователь blocked, его лучше либо не пускать в систему, либо запрещать критичные действия отдельной проверкой.

  Структура маршрутов

  Пример структуры:

  - /admin -> dashboard
  - /admin/roles -> CRUD ролей
  - /admin/users -> CRUD пользователей
  - /admin/products -> CRUD товаров
  - /admin/orders -> CRUD заказов

  Имена маршрутов:

  - admin.dashboard
  - admin.roles.index, admin.roles.create, admin.roles.store, ...
  - admin.users.index, ...
  - admin.products.index, ...
  - admin.orders.index, ...

  Лучше оформить через Route::prefix('admin')->name('admin.')->middleware([...]).

  Структура контроллеров

  Нужны отдельные административные контроллеры:

  - Admin/DashboardController
  - Admin/RoleController
  - Admin/UserController
  - Admin/ProductController
  - Admin/OrderController

  Ответственность контроллеров:

  - принять FormRequest
  - собрать DTO
  - вызвать сервис
  - вернуть view/redirect

  Контроллер не должен считать суммы заказа, назначать роли или менять статус напрямую.

  Структура сервисов

  Простая схема сервисов:

  - RoleService
      - список ролей
      - создание
      - обновление
      - удаление
      - назначение ролей пользователю
  - UserService
      - создание
      - обновление
      - удаление
      - смена статуса
      - сброс пароля
      - синхронизация ролей
  - ProductService
      - создание
      - обновление
      - удаление
      - смена статуса
  - OrderService
      - создание заказа
      - обновление
      - удаление
      - смена статуса
      - пересчёт итоговой суммы

  Бизнес-правила, например “нельзя завершить пустой заказ” или “при удалении роли нельзя удалить системную роль admin”, должны жить именно в сервисе.

  Структура DTO

  DTO лучше делать под операции:

  - CreateRoleDto, UpdateRoleDto
  - CreateUserDto, UpdateUserDto, ResetUserPasswordDto
  - CreateProductDto, UpdateProductDto
  - CreateOrderDto, UpdateOrderDto, UpdateOrderStatusDto

  DTO содержит только данные, без логики. Это делает сервисы чище и предсказуемее.

  Структура валидации

  Отдельные FormRequest классы:

  - Admin/RoleStoreRequest, RoleUpdateRequest
  - Admin/UserStoreRequest, UserUpdateRequest, UserPasswordResetRequest, UserStatusRequest
  - Admin/ProductStoreRequest, ProductUpdateRequest, ProductStatusRequest
  - Admin/OrderStoreRequest, OrderUpdateRequest, OrderStatusRequest

  Проверки:

  - уникальность email
  - допустимые статусы
  - корректные роли
  - положительная цена
  - stock >= 0
  - наличие товаров в заказе
  - quantity >= 1

  Структура страниц интерфейса

  Логичная админка для учебного проекта:

  - Sidebar:
      - Dashboard
      - Users
      - Roles
      - Products
      - Orders
  - Dashboard:
      - карточки со счётчиками
      - users count
      - products count
      - orders count
      - active orders count
      - completed orders count
  - Users:
      - таблица
      - фильтр по статусу
      - просмотр
      - форма создания/редактирования
      - блок ролей
      - список заказов пользователя
  - Roles:
      - таблица ролей
      - форма создания/редактирования
  - Products:
      - таблица
      - форма товара
      - изображение
      - статус
  - Orders:
      - таблица
      - просмотр состава заказа
      - смена статуса
      - адрес доставки
      - итоговая сумма

  Порядок реализации

  1. Добавить таблицы roles, role_user, поля users.status, users.avatar, products.status.
  2. Создать модели Role и связи в User.
  3. Добавить seed начальных ролей: user, admin, manager.
  4. Реализовать middleware проверки роли и защитить /admin.
  5. Сделать DashboardController и главную страницу админки со статистикой.
  6. Реализовать CRUD ролей.
  7. Реализовать CRUD пользователей с назначением ролей, статусом и сбросом пароля.
  8. Реализовать CRUD товаров.
  9. Реализовать CRUD заказов и смену статусов.
  10. Добавить Blade-шаблоны админки и навигацию.
  11. Покрыть ключевые сценарии Feature-тестами:

  - доступ в админку
  - запрет для обычного пользователя
  - CRUD ролей
  - назначение ролей
  - смена статусов
  - создание/редактирование заказов
```

### Комментарий к задаче:
Также прочитай все что лежит в docs

### Файл, которые нужно проанализировать:


#### Пример:
```

```
