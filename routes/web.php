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

use Illuminate\Http\Request;

$router->group(['prefix' => 'api'], function () use ($router) {
    // Route to authenticate and obtain the Bearer Token
    $router->post('authenticate', 'AuthController@getToken');

    // Protected routes that require the Bearer Token
    $router->group(['middleware' => 'custom.token.authorization'], function () use ($router) {
        // Route to login using the obtained Bearer Token
        $router->post('login', 'AuthController@login');

        // Add more protected routes here
    });
});

$router->group(['prefix' => 'project', 'middleware' => 'customtokenauthorization'], function () use ($router) {
    $router->get('/', 'TicketController@getProjects');
    $router->get('{project}', 'TicketController@getIssues');
    $router->get('user/{project}', 'TicketController@getUsers');
    $router->post('/update/{projectName}/transition', 'TicketController@updateIssue');
    $router->post('create', 'TicketController@createIssue');
    $router->group(['prefix' => '{project}/issue'], function () use ($router) {



    });
});