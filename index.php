<?php
require_once 'config/config.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';

// Router initialisieren
$router = new Router();

// Routen definieren
$router->add('GET', '', 'HomeController@index');
$router->add('GET', 'home', 'HomeController@index');

$router->add('GET', 'user', 'UserController@index');
$router->add('GET', 'user/{id}', 'UserController@show');

// Dashboard
$router->add('GET', 'dashboard', 'DashboardController@index');

// Mitglieder bearbeiten
$router->add('GET', 'member/edit/{id}', 'MemberController@edit');
$router->add('POST', 'member/update/{id}', 'MemberController@update');

// URL verarbeiten
$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);