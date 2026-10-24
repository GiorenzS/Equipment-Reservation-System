<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Authentication routes
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::registerPost');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::loginPost');
$routes->get('logout', 'AuthController::logout');
