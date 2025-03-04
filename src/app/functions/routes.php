<?php
// functions.php

function getRoute($page) {
    // Exemple de routes (ajustez selon votre structure de projet)
    $routes = [
        'explore' => '/explore.php',
        'about' => '/about.php',
        'contact' => '/contact.php',
        'login' => 'src/app/view/login.php',
        'logout' => '/',
         'search' => '/findservice/'
    ];
    return $routes[$page] ?? '#';
}
?>