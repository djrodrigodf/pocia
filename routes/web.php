<?php

use App\Livewire\DebitNegociation;
use App\Livewire\Impression;
use App\Livewire\ListDebits;
use App\Livewire\PedidoItem;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Livewire\Welcome;

Route::get('/', Welcome::class)
    ->name('home');


Route::get('login', Login::class)
    ->middleware('guest')
    ->name('login');


Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');




