<?php

namespace App\DTO;

use App\Http\Requests\UpdateProfileRequest;
use Spatie\LaravelData\Data;

class UpdateProfileDto extends Data
{
    public function __construct(
        public ?string $first_name,
        public ?string $last_name,
        public ?string $email,
        public ?string $phone,
    ) {}

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            $request->validated()['first_name'],
            $request->validated()['last_name'],
            $request->validated()['email'],
            $request->validated()['phone'],
        );
    }
}
