<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Admin extends User
{
    protected $table = 'users';


    protected static function booted()
    {
        static::addGlobalScope('admin_type', function (Builder $builder) {
            $builder->where('type', 'Admin');
        });
    }
}
