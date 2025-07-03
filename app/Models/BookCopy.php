<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookCopy extends Model
{
    public $timestamps = true;

    protected $primaryKey = 'barcode';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'barcode',
        'condition',
        'location',
        'is_available',
        'book_id'
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }
}
