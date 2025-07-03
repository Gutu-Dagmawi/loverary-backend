<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CheckIn extends Model
{
    public function check_out(): HasOne
    {
        return $this->hasOne(CheckOut::class);
    }
}
