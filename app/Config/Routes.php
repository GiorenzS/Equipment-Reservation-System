<?php

/** @var \CodeIgniter\Router\RouteCollection $routes */

// Public authentication routes
$routes->get('register', 'AuthController::register');
$routes->get('signup', 'AuthController::register');
$routes->post('register', 'AuthController::registerPost');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::loginPost');
$routes->get('logout', 'AuthController::logout');

// Protected routes (requires authentication)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('dashboard', 'Home::index');

    // Equipment routes
    $routes->get('equipment', 'EquipmentController::index');
    $routes->get('equipment/catalog', 'EquipmentController::index');
    $routes->get('equipment/(:num)', 'EquipmentController::show/$1');

    // Reservation routes
    $routes->get('reservations', 'ReservationController::index');
    $routes->get('reservations/(:num)', 'ReservationController::show/$1');
    $routes->get('reservations/create/(:num)', 'ReservationController::create/$1');
    $routes->post('reservations/store', 'ReservationController::store');

    $routes->group('admin', ['filter' => 'admin'], static function ($routes) {
        $routes->get('reservations', 'AdminController::index');
        $routes->post('reservations/(:num)/status/(:segment)', 'AdminController::updateReservationStatus/$1/$2');
        $routes->post('reservations/(:num)/checkout', 'AdminController::checkout/$1');
        $routes->post('transactions/(:num)/return', 'AdminController::returnItem/$1');
    });
});