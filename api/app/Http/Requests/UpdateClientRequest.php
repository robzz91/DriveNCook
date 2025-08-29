<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('client');
        return [
            'nom'   => ['required','string','max:100'],
            'email' => ['required','email','max:150',"unique:clients,email,{$id}"],
        ];
    }
}
