<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// CTA Arrivals
$routes->get('arrivals', 'Arrivals::index');

// Legacy-style navigation
$routes->get('lines', 'Lines::index');
$routes->get('lines/(:segment)', 'Lines::stations/$1');
