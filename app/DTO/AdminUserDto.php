<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class AdminUserDto extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone,
        public string $status,
        public ?string $password,
        public int $roleId,
        public ?UploadedFile $avatar,
    ) {
    }

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            $request->validated('first_name'),
            $request->validated('last_name'),
            $request->validated('email'),
            $request->validated('phone'),
            $request->validated('status'),
            $request->validated('password'),
            (int) $request->validated('role_id'),
            $request->file('avatar')
        );
    }
}
