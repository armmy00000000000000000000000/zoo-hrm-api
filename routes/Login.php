<?php
$router->group(['prefix' => '/api/v1'], function () use ($router) {

    $router->post('Register', 'UserController@addData');    
    $router->post('Login', 'LoginController@Login');

});