<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'namespace' => 'Api',
    'prefix' => 'api',
], function (\Laravel\Lumen\Routing\Router $router) {
    require_once __DIR__ . "/auth.php";

    $router->get("/", "IndexController@index");

//    device相关
    $router->get("/device/get_status", "DeviceController@getStatus");


//    user相关
//    $router->get("/user/get_left_times", ["middleware"=>["auth"], "uses"=>"UserController@getLeftTimes"]);
    $router->addRoute(['GET', 'POST'], "/user/get_left_times", ["middleware"=>["auth"], "uses"=>"UserController@getLeftTimes"]);
    $router->addRoute(['GET', 'POST'], "/user/use_toilet_paper", ["middleware"=>["auth"], "uses"=>"UserController@useToiletPaper"]);

});