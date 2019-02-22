<?php

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

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});

$router->get('/', ['as' => 'index', 'uses' => 'IndexController@index']);
$router->get('t', ['as' => 'time', 'uses' => 'Controller@show']);
//Branch routes
$router->group(['prefix' => 'branch', 'as' => 'branch'], function () use ($router) {
    $router->get('current', ['as' => 'current', 'uses' => 'BranchController@current']);
    $router->get('list', ['as' => 'list', 'uses' => 'BranchController@list']);
});