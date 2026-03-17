<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class RoleDto extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {
    }

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            $request->validated('name'),
            Str::lower($request->validated('slug'))
        );
    }
}
