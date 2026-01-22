<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KmsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // Require authenticated user
    }

    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'required', 'string', 'max:255'],
            'category_id'  => ['sometimes', 'required', 'integer', 'exists:kms_cat,cat_id'],
            'link'         => ['nullable', 'url', 'max:2048'],
            'visibility'   => ['nullable', 'in:0,1'],
            'status'       => ['nullable', 'in:0,1'],
            'thumbnail'    => ['nullable', 'string', 'max:2048'],
        ];
    }
}
