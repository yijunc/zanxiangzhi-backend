<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'namespace' => 'Api',
    'prefix' => 'api',
], function (\Laravel\Lumen\Routing\Router $router) {
    require_once __DIR__ . "/auth.php";

    $router->get("/", "IndexController@index");
    $router->get("/device/get_status", "DeviceController@getStatus");
    $router->get("/location/get_nearby_locations", "LocationController@getNearbyLocations");
});