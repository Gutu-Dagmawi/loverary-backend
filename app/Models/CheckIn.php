<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CheckIn extends Model
{
    protected $primaryKey = 'check_in_id';
    protected $fillable = [
        'check_out_id',
    ];
    public function check_out(): HasOne
    {
        return $this->hasOne(CheckOut::class);
    }
}
