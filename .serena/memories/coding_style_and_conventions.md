# Стиль и соглашения

- Базовые правила из `.editorconfig`: UTF-8, LF, пробелы, отступ 4 для PHP.
- PHP-стиль: PSR-12, где применимо `declare(strict_types=1);`.
- Именование:
  - Классы PSR-4: `ProductService`, `OrderStoreRequest`, `RegisterDto`.
  - Blade partials в snake_case (например, `resources/views/cart/_content.blade.php`).
- Разделение ответственности:
  - `FormRequest` отвечает за валидацию.
  - `DTO` за перенос/типизацию входных параметров.
  - `Service` за бизнес-операции.
  - Контроллер организует поток запроса и ответа.
- Роутинг: основная маршрутизация в `routes/web.php`, админка защищена `auth` + `role:admin`.
- Тесты:
  - Интеграционные/HTTP в `tests/Feature`.
  - Изолированная логика в `tests/Unit`.
  - Имена тестов: `*Test.php`.
- Коммиты: короткие conventional-префиксы (`feat:`, `fix:`, `style:` и т.д.), императивный стиль сообщения.