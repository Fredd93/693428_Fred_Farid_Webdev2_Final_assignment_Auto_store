<?php
use GTA\Controllers\AuthController;

Route::add('/api/auth/login',    fn() => (new AuthController())->login(),    'POST');
Route::add('/api/auth/register', fn() => (new AuthController())->register(), 'POST');
