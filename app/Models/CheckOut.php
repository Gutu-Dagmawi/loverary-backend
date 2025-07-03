<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckOut extends Model
{
    public function check_in(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }


    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
