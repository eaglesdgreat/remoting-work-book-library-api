<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user();
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
            'author_id' => 'required|integer|exists:authors',
            'description' => 'required|string',
            'image' => 'required|string',
            'publisher' => 'required|string',
            'published_date' => 'required|date',
            'book' => 'required|string',
        ];
    }
}
