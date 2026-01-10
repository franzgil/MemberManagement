<?php
// Basis-URL aus Server-Variablen automatisch erkennen
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($scriptName, '/') . '/';

define('BASE_URL', $baseUrl);

// Optionale weitere Konstante (praktisch für absolute URLs):
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
define('FULL_BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . BASE_URL);