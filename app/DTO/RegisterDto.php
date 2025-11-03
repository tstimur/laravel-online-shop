<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Http\Requests\RegisterRequest;
use Spatie\LaravelData\Data;

class RegisterDto extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            $request->validated('first_name'),
            $request->validated('last_name'),
            $request->validated('email'),
            $request->validated('password'),
        );
    }
}
