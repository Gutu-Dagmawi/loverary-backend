<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends User
{
    protected $table = 'users';
    public function check_outs(): HasMany
    {
        return $this->hasMany(CheckOut::class);
    }
}
