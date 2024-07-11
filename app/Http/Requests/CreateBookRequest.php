<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class CreateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
            'description' => 'required|string',
            'image' => [
                'required',
                File::image(),
            ],
            'publisher' => 'required|string',
            'published_date' => 'required|date',
            'book' => [
                'required',
                 File::types(['pdf']),
            ],
        ];
    }
}
