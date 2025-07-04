<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends User
{
    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('member_type', function (Builder $builder) {
            $builder->where('type', 'Member');
        });
    }

    public function check_outs(): HasMany
    {
        return $this->hasMany(CheckOut::class);
    }
}
