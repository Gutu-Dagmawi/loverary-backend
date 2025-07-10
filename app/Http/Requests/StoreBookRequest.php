<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . ((int) date('Y') + 1),
            'availability_status' => 'required|boolean',
            'publisher' => 'nullable|string|max:255',
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'category' => 'required|string|max:255',
            'edition' => 'nullable|string|max:255',
            'language' => 'required|string|max:255',
            'pages' => 'required|integer|min:1|max:10000',
            'summary' => 'nullable|string',
            'author_id' => 'nullable|exists:authors,author_id',
            'book_copies' => 'required|array',
            'book_copies.*.barcode' => 'required|string|max:100|unique:book_copies,barcode',
            'book_copies.*.condition' => 'nullable|string|max:255',
            'book_copies.*.location' => 'nullable|string|max:255',
            'book_copies.*.is_available' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
