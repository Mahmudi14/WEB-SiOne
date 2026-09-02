<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' =>
            'Pilih file CSV terlebih dahulu.',

            'file.file' =>
            'File yang dipilih tidak valid.',

            'file.mimes' =>
            'File harus berformat CSV.',

            'file.max' =>
            'Ukuran file CSV maksimal 5 MB.',
        ];
    }
}