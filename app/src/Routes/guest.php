<?php
use GTA\Controllers\GuestOrderController;

Route::add('/api/orders/guest/request-code', fn() => (new GuestOrderController())->requestCode(), 'POST');
Route::add('/api/orders/guest',               fn() => (new GuestOrderController())->store(),       'POST');
