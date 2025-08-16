<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});



// Define a route for the employee_management page
$router->group(['prefix' => 'hrm/api/v1', 'middleware' => 'auth'], function () use ($router) {
    /// employee management

    // employee
    $router->post('list_employee', 'Employee_Management\Controller_employee@list_employee');

    // family_info


    // job_and_department
    

    // personal_info
    $router->get('view_personal_info/{id}', 'Employee_Management\Controller_personal_info@view_personal_info');
    $router->post('edit_personal_info', 'Employee_Management\Controller_personal_info@update_personal_info');
  

    // work_history


});