## Требования к задаче:
1. Не редактировать глобальные файлы. Редактировать только указанные файлы в @AGENTS.md или в прямом сообщении тебе.
2. При выполнении задачи ты показываешь подробный план решения с расширенной аргументацией решения. Затем после ознакомления мной, я даю подтверждение на редактирование.
3. При каждом моем запросе, ты всегда проверяешь содержимое @AGENTS.md и выводишь содержимое в консоли.
4. При каждом моем запросе, ты всегда анализируешь самого начала файлы указанные в задании в @AGENTS.md.
5. Любые твои комментарии должны быть на русском языке
6. Опирайся уже на существующую архитектуру проекта


## Исключения при выполнении задачи:
1. Если необходимо редактирование файла не указанного в задании, то сначала ты также готовишь решение, аргументируешь, а затем запрашиваешь мое подтверждение


## Начало задачи
### Формулировка задачи:
Нужно реализовать:
1) выводить список товаров из базы;
2) использовать DTO для чистой передачи параметров;
3) обрабатывать параметры запроса через Request, если необходимы для пагинации;
4) выносить бизнес-логику в сервисный слой;
5) делать пагинацию с фиксированными значениями;
6) отображать список через Blade-шаблон.

### Комментарий к задаче:
1. В задаче должны использоваться:
    - Controllers,
    - Service Layer,
    - DTO (Spatie\LaravelData),
    - Request Validation,
    - Eloquent,
    - Blade,
    - Query Builder,
    - Pagination.
2. Это материал для начинающих на Laravel
3. Учитывай, что далее будут выполнять уроки:
    - Страница товара
    - Сортировка, фильтрация, базовый поиск
    - Категории товаров
    - Полнотекстовый поиск на ElasticSearch
4. При написании сервиса, контроллера, реквеста, дто опирайся на существующие похожие классы

## Конец задачи

## Пример кода
```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // пользователь может выбрать только варианты, разрешённые нами
            'per_page' => ['nullable', 'in:10,25,50,100'],
        ];
    }
}

namespace App\DTO;

use App\Http\Requests\ProductFilterRequest;
use Spatie\LaravelData\Data;

class ProductFilterDto extends Data
{
    public function __construct(
        public int $per_page = 10, // фиксированное значение по умолчанию
    ) {}

    public static function fromRequest(ProductFilterRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['per_page'] ?? 10,
        );
    }
}

namespace App\Services;

use App\DTO\ProductFilterDto;
use App\Models\Product;

class ProductService
{
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function getProducts(ProductFilterDto $dto)
    {
        $query = Product::query();

        // Количество на страницу — всегда контролируется сервисом
        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true)
            ? $dto->per_page
            : 10;

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }
}

namespace App\Http\Controllers;

use App\DTO\ProductListDto;
use App\Http\Requests\ProductListRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    public function index(ProductListRequest $request)
    {
        // Преобразуем Request → DTO
        $dto = ProductListDto::fromRequest($request);

        // Получаем товары через сервис
        $products = $this->productService->getPaginatedProducts($dto);

        return view('products.index', [
            'products' => $products,
            'perPage'  => $dto->perPage,
        ]);
    }
}

```

## Пример шаблона
```bladehtml
@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
<div class="container py-4">

    <h1 class="mb-4">Каталог товаров</h1>

    {{-- Выбор количества товаров --}}
    <form method="GET" action="{{ route('products.index') }}" class="mb-3">
        <label>Товаров на странице:</label>
        <select name="per_page" onchange="this.form.submit()" class="form-select w-auto d-inline-block ms-2">
            @foreach([10,25,50,100] as $option)
            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
            {{ $option }}
            </option>
            @endforeach
        </select>
    </form>


    {{-- Список товаров --}}
    <div class="row g-4">
        @forelse($products as $product)
        <div class="col-md-3 col-sm-6 col-12">
            <div class="card h-100">

                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     class="card-img-top" alt="{{ $product->name }}">
                @endif

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>

                    <p class="fw-semibold">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>

                    <a href="{{ route('products.show', $product) }}"
                       class="btn btn-outline-primary mt-auto">
                        Подробнее
                    </a>
                </div>
            </div>
        </div>
        @empty
        <p>Товары еще не добавлены.</p>
        @endforelse
    </div>


    {{-- Пагинация --}}
    <div class="mt-4">
        {{ $products->appends(['per_page' => $perPage])->links() }}
    </div>

</div>
@endsection


```
