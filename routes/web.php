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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('/auth/signup', 'UserController@signUp');
    $router->post('/auth/signin', 'UserController@login');
    $router->get('/profile', ['middleware' => 'jwt.auth', 'uses' => 'UserController@viewProfile']);
    $router->put('/profile', ['middleware' => 'jwt.auth', 'uses' => 'UserController@updateProfile']);
    $router->post('/entries', ['middleware' => 'jwt.auth', 'uses' => 'EntryController@addEntry']);
    $router->get('/entries', ['middleware' => 'jwt.auth', 'uses' => 'EntryController@getAllEntries']);
    $router->get('/entries/{id}', ['middleware' => 'jwt.auth', 'uses' => 'EntryController@getEntry']);
    $router->put('/entries/{id}', ['middleware' => 'jwt.auth', 'uses' => 'EntryController@updateEntry']);
    $router->delete('/entries/{id}', ['middleware' => 'jwt.auth', 'uses' => 'EntryController@deleteEntry']);
});