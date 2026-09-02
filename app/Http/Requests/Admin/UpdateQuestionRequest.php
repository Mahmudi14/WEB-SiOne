<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => [
                'required',
                'string',
                'max:2000',
            ],

            'options' => [
                'required',
                'array',
                'min:2',
                'max:4',
            ],

            'options.*.text' => [
                'required',
                'string',
                'max:1000',
            ],

            'options.*.is_correct' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' =>
            'Pertanyaan wajib diisi.',

            'options.required' =>
            'Pilihan jawaban wajib diisi.',

            'options.min' =>
            'Minimal terdapat 2 pilihan jawaban.',

            'options.max' =>
            'Maksimal terdapat 4 pilihan jawaban.',

            'options.*.text.required' =>
            'Pilihan jawaban wajib diisi.',
        ];
    }

    public function withValidator(
        Validator $validator,
    ): void {
        $validator->after(function (
            Validator $validator,
        ) {
            $options = collect(
                $this->input('options', [])
            );

            $correctCount = $options
                ->filter(function ($option) {
                    return filter_var(
                        data_get(
                            $option,
                            'is_correct',
                            false,
                        ),
                        FILTER_VALIDATE_BOOLEAN,
                    );
                })
                ->count();

            if ($correctCount !== 1) {
                $validator
                    ->errors()
                    ->add(
                        'options',
                        'Pilih tepat satu jawaban yang benar.',
                    );
            }
        });
    }
}
