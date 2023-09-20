<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->post('login', 'AuthController@login');

$router->group(['prefix' => 'project'], function () use ($router) {
    $router->get('employee', 'TicketController@getEmployees');
    $router->get('/', 'TicketController@getProjects');
    $router->get('{project}', 'TicketController@getIssues');
    $router->get('user/{project}', 'TicketController@getUsers');
    /******** */
    $router->post('/update/{projectName}/transition', 'TicketController@updateIssue');
    $router->post('create', 'TicketController@createIssue');
});