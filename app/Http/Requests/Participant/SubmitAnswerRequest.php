<?php

namespace App\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_option_id' => [
                'required',
                'integer',
                'exists:question_options,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'question_option_id.required' =>
            'Pilih salah satu jawaban terlebih dahulu.',

            'question_option_id.exists' =>
            'Pilihan jawaban tidak valid.',
        ];
    }
}
