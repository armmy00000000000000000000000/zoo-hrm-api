<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Define a route for the recruitment_onboarding page
$router->group(['prefix' => 'hrm/api/v1', 'middleware' => 'auth'], function () use ($router) {
    /// agency
   $router->get('list_agency', 'Resource\Controller_Resource@getAll_agency');
   $router->post('add_agency', 'Resource\Controller_Resource@Addagency');
   $router->get('view_agency/{id}', 'Resource\Controller_Resource@getID_agency');
   $router->post('add_division', 'Resource\Controller_Resource@Add_division');
   $router->post('add_department', 'Resource\Controller_Resource@Add_department');
   $router->put('edit_division/{id}', 'Resource\Controller_Resource@Edit_division');
   $router->put('edit_department/{id}', 'Resource\Controller_Resource@Edit_department');
   $router->delete('delete_division/{id}', 'Resource\Controller_Resource@Delete_division');
   /// User
   $router->get('list_user', 'Resource\Controller_Resource@getAll_user');
   $router->get('view_user/{id}', 'Resource\Controller_Resource@getID_user');
   $router->post('add_user', 'Resource\Controller_Resource@Adduser');
   $router->put('edit_user/{id}', 'Resource\Controller_Resource@Updateuser');
   $router->put('update_password/{id}', 'Resource\Controller_Resource@update_password');
   /// Roles
   $router->get('list_roles', 'Resource\Controller_Resource@list_roles');
   $router->get('view_roles/{id}', 'Resource\Controller_Resource@view_roles');
   $router->post('add_roles', 'Resource\Controller_Resource@Add_roles');
   $router->put('edit_roles/{id}', 'Resource\Controller_Resource@edit_roles');    
   $router->post('add_roles_permission', 'Resource\Controller_Resource@add_roles_permission');
   $router->post('delete_roles_permission', 'Resource\Controller_Resource@delete_roles_permission');
   $router->delete('delete_roles/{id}', 'Resource\Controller_Resource@delete_roles');
   /// Permissions
   $router->get('list_permission', 'Resource\Controller_Resource@list_permission');
   $router->get('view_permission/{id}', 'Resource\Controller_Resource@view_permission');
   $router->put('edit_permission/{id}', 'Resource\Controller_Resource@edit_permission');    
   $router->post('add_permission', 'Resource\Controller_Resource@add_permission');
   $router->delete('delete_permission/{id}', 'Resource\Controller_Resource@delete_permission');



});


/// ไม่ใช้ token 
$router->group(['prefix' => 'hrm/api/v1'], function () use ($router) {
     $router->get('list_agency', 'Resource\Controller_Resource@getAll_agency');
});