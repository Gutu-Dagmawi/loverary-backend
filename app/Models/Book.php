<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'publication_year',
        'availability_status',
        'publisher',
        'isbn',
        'category',
        'edition',
        'language',
        'pages',
        'summary',
        'author_id',
    ];

    protected $primaryKey = 'book_id';

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }


    public function bookCopies(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'book_id', 'book_id');
    }
}
