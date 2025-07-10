<?php
// app/Http/Requests/UpdateBookRequest.php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateBookRequest extends StoreBookRequest
{
    public function rules(): array
    {
        $rules = parent::rules();


        // Override ISBN unique rule to ignore current book
        $bookId = $this->route('book')?->book_id ?? $this->route('book');

        $rules['isbn'] = "required|string|max:20|unique:books,isbn,{$bookId},book_id";


        foreach ($this->input('book_copies', []) as $index => $copy) {
            if (isset($copy['barcode'])) {
                $copyId = $copy['book_copy_id'] ?? null;
                $rules["book_copies.$index.barcode"] = [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('book_copies', 'barcode')->ignore($copyId, 'book_copy_id')
                ];
            }
        }


        // Make other fields sometimes (except nullable and isbn/barcode)
        foreach ($rules as $field => $rule) {
            if (!str_contains($field, 'book_copies.*.barcode') && !str_contains($rule, 'nullable')) {
                $rules[$field] = $rule . '|sometimes';
            }
        }

        return $rules;
    }


}
