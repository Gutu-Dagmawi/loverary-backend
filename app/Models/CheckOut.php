<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class CheckOut extends Model
{
    protected $primaryKey = 'check_out_id';

    protected $fillable = [
        'member_id',
        'due_date',
        'book_copy_barcode',
        'check_in_id',
    ];

    protected $appends = ['status'];

    // Accessors
    public function getStatusAttribute(): string
    {
        if ($this->check_in_id !== null) {
            return 'returned';
        }

        if ($this->due_date && Carbon::parse($this->due_date)->isPast()) {
            return 'overdue';
        }

        return 'checked_out';
    }


    //Relationships
    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class, 'check_in_id');
    }

    public function bookCopy(): HasOne
    {
        return $this->hasOne(BookCopy::class, 'barcode', 'book_copy_barcode');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
