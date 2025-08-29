<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom'   => ['required','string','max:100'],
            'email' => ['required','email','max:150','unique:clients,email'],
        ];
    }
}
