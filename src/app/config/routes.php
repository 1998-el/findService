<?php
// routes.php

$routes = [
    'home' => 'index.php',
    'explore' => 'explore.php',
    'about' => 'about.php',
    'contact' => 'contact.php',
    'login' => 'Login.php',

];

// Function to get the route path
function getRoute($name) {
    global $routes;
    return isset($routes[$name]) ? $routes[$name] : null;
}
?>
