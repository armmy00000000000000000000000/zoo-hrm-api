<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});



// Define a route for the employee_management page
$router->group(['prefix' => 'hrm/api/v1', 'middleware' => 'auth'], function () use ($router) {
    $router->get('getAll/{id}', 'UserController@getAll');
    $router->get('getID/{id}', 'UserController@getID');
    $router->post('insertData', 'UserController@addData');
    $router->put('updateData/{id}', 'UserController@updateUser');
    $router->delete('deleteData/{id}', 'UserController@deleteUser');
    $router->post('/uploadFile', 'UploadController@upload');

});