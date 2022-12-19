<?php

$router->group(['prefix' => 'auth'], function () use ($router) {
    // $router->get('register')
    $router->post('login', 'AuthAccessController@login');
    $router->post('refresh', 'AuthAccessController@refresh');
    $router->get('logout', [
        'middleware' => 'auth',
        'uses' => 'AuthAccessController@logout',
    ]);
});

$router->get('/', ['middleware' => 'auth', function () use ($router) {
    return $router->app->version();
}]);
