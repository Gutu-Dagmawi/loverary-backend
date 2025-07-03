<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $primaryKey = 'author_id';
    protected $fillable = [
        'full_name',
        'gender'
    ];
    public function books (): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
