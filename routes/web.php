<?php

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthAccessController@login');
    // $router->get('register')
    $router->post('refresh', 'AuthAccessController@refresh');
});

$router->get('/', ['middleware' => 'auth', function () use ($router) {
    return $router->app->version();
}]);
