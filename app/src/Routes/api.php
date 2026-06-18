<?php
use GTA\Controllers\CarController;
use GTA\Controllers\OrderController;
use GTA\Controllers\UserController;
use GTA\Controllers\AppointmentController;
use GTA\Controllers\MotdController;

// Cars
Route::add('/api/cars',               fn() => (new CarController())->index(),   'GET');
Route::add('/api/cars/filters',       fn() => (new CarController())->filters(), 'GET');
Route::add('/api/cars/([0-9]+)',       fn($id) => (new CarController())->show((int)$id),    'GET');
Route::add('/api/cars',               fn() => (new CarController())->store(),   'POST');
Route::add('/api/cars/([0-9]+)',       fn($id) => (new CarController())->update((int)$id),  'PUT');
Route::add('/api/cars/([0-9]+)',       fn($id) => (new CarController())->destroy((int)$id), 'DELETE');

// Orders
Route::add('/api/orders/export',      fn() => (new OrderController())->export(), 'GET');
Route::add('/api/orders',             fn() => (new OrderController())->index(),  'GET');
Route::add('/api/orders/([0-9]+)',     fn($id) => (new OrderController())->show((int)$id),   'GET');
Route::add('/api/orders',             fn() => (new OrderController())->store(),  'POST');
Route::add('/api/orders/([0-9]+)',     fn($id) => (new OrderController())->update((int)$id), 'PUT');

// Appointments
Route::add('/api/appointments',           fn() => (new AppointmentController())->index(),          'GET');
Route::add('/api/appointments',           fn() => (new AppointmentController())->store(),          'POST');
Route::add('/api/appointments/([0-9]+)',   fn($id) => (new AppointmentController())->update((int)$id),  'PUT');
Route::add('/api/appointments/([0-9]+)',   fn($id) => (new AppointmentController())->destroy((int)$id), 'DELETE');

// Users
Route::add('/api/users',              fn() => (new UserController())->index(),              'GET');
Route::add('/api/users',              fn() => (new UserController())->store(),              'POST');
Route::add('/api/users/([0-9]+)',      fn($id) => (new UserController())->show((int)$id),   'GET');
Route::add('/api/users/([0-9]+)',      fn($id) => (new UserController())->update((int)$id), 'PUT');
Route::add('/api/users/([0-9]+)',      fn($id) => (new UserController())->destroy((int)$id),'DELETE');

// MOTD
Route::add('/api/motd', fn() => (new MotdController())->show(),   'GET');
Route::add('/api/motd', fn() => (new MotdController())->update(), 'PUT');
