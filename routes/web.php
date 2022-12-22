<?php

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('register', 'AuthAccessController@register');
    $router->post('login', 'AuthAccessController@login');
    $router->post('refresh', 'AuthAccessController@refresh');
    $router->get('logout', [
        'middleware' => 'auth',
        'uses' => 'AuthAccessController@logout',
    ]);
});

$router->group([
    'middleware' => ['auth', 'verified', 'moderation'],
    'prefix' => 'moderation'
], function () use ($router) {
    $router->get('users', 'ModerationController@users');
    $router->get('verify/{userId}', 'ModerationController@verify');
});
