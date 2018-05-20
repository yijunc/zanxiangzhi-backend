<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/20/18
 * Time: 1:43 PM
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Junning\GeoHash\GeoHash;

class LocationController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getNearbyLocations(Request $request){
        $this->validate($request, [
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric'
        ]);
        $longtitude = $request->input("longitude");
        $latitude = $request->input("latitude");
        $geohash = new GeoHash();
        $current_loction_hash = $geohash->encode($latitude, $longtitude);
        $location_prefix = substr($current_loction_hash, 0, 7);
        $neighbours = $geohash->neighbors($location_prefix);
        array_push($neighbours, $location_prefix);
        //print_r($neighbours);
        return s("ok", [
            "neib" => $neighbours
        ]);
    }

}