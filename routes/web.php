<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// include ไฟล์ routes อื่น ๆ
require __DIR__.'/employee_management.php';
require __DIR__.'/recruitment_onboarding.php';
require __DIR__.'/Login.php';
require __DIR__.'/Resource.php';











