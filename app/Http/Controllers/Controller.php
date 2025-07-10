<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function abortUnlessAdmin(): void
    {
        if (!auth()->user() instanceof \App\Models\Admin) {
            abort(403, 'Forbidden. Only admins are allowed.');
        }
    }
}
