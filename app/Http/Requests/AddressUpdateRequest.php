<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
{
    protected $errorBag = 'addressUpdate';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->normalizedFields());
    }

    /**
     * @return array<string, string|null>
     */
    private function normalizedFields(): array
    {
        $fields = ['label', 'city', 'street', 'house', 'apartment'];
        $data = [];

        foreach ($fields as $field) {
            if ($this->has($field)) {
                $data[$field] = $this->normalizeString($this->input($field));
            }
        }

        return $data;
    }

    private function normalizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'min:2', 'max:255'],
            'street' => ['required', 'string', 'min:2', 'max:255'],
            'house' => ['required', 'string', 'min:1', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'label' => ['nullable', 'string', 'max:50'],
        ];
    }
}
