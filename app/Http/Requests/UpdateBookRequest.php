<?php
// app/Http/Requests/UpdateBookRequest.php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateBookRequest extends StoreBookRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Use route parameter 'book_id'
        $bookId = $this->route('book')?->book_id;


        // Override ISBN unique rule to ignore current book
        $rules['isbn'] = "required|string|max:20|unique:books,isbn,{$bookId},book_id";


        // Override barcode unique rules dynamically
        foreach ($this->input('book_copies', []) as $index => $copy) {
            if (isset($copy['barcode'])) {
                $copyBarcode = $copy['barcode'];
                $rules["book_copies.$index.barcode"] =
                    "required|string|max:100|unique:book_copies,barcode,{$copyBarcode},barcode";
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
