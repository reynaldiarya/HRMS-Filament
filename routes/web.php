<?php

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

$redirectBasedOnRole = function () {
    if (auth()->check()) {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return redirect()->to(Filament::getPanel('admin')->getUrl());
        }

        if ($user->hasRole('hr')) {
            return redirect()->to(Filament::getPanel('hr')->getUrl());
        }

        if ($user->hasRole('employee')) {
            return redirect()->to(Filament::getPanel('employee')->getUrl());
        }

        auth()->logout();
    }

    return redirect('/login');
};

Route::get('/', function () use ($redirectBasedOnRole) {
    if (auth()->check()) {
        return $redirectBasedOnRole();
    }

    return redirect('/login');
});

Route::get('/login', Login::class)
    ->middleware('guest')
    ->name('login');
