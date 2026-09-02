<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $room = $this->route(
            'room',
        );

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:20',

                Rule::unique(
                    'rooms',
                    'code',
                )->ignore(
                    $room?->id,
                ),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'shuffle_questions' => [
                'required',
                'boolean',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (
            $this->filled('code')
        ) {
            $this->merge([
                'code' =>
                strtoupper(
                    trim(
                        (string) $this->input(
                            'code',
                        ),
                    ),
                ),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' =>
            'Nama room wajib diisi.',

            'code.required' =>
            'Kode room wajib diisi.',

            'code.unique' =>
            'Kode room sudah digunakan.',

            'code.max' =>
            'Kode room maksimal 20 karakter.',
        ];
    }
}