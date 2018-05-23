<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'namespace' => 'Api',
    'prefix' => 'api',
], function (\Laravel\Lumen\Routing\Router $router) {
    require_once __DIR__ . "/auth.php";


    //    device相关
    $router->get("/device/get_status", "DeviceController@getStatus");
    $router->get("/device/get_devices_by_location_id", "DeviceController@getDevicesByLocationId");

    //    user相关
    //    $router->get("/user/get_left_times", ["middleware"=>["auth"], "uses"=>"UserController@getLeftTimes"]);
    //    $router->addRoute(['GET', 'POST'], "/user/get_left_times", ["middleware" => ["auth"], "uses" => "UserController@getLeftTimes"]);
    $router->addRoute(['GET', 'POST'], "/user/use_toilet_paper", ["middleware" => ["auth"], "uses" => "UserController@useToiletPaper"]);


    //    location related
    $router->get("/location/get_nearby_locations", "LocationController@getNearbyLocations");
    $router->get("/location/add_location", "LocationController@addLocation");
});
