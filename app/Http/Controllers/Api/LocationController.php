<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/20/18
 * Time: 1:43 PM
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

use Junning\GeoHash\GeoHash;

class LocationController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function addLocation(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $location = new Location();
        $location->create([
            'name' => $request->input('name'),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'geo_hash' => (new GeoHash())->encode($latitude, $longitude),
            'desc' => $request->input('desc')
        ]);
        return s('ok');
    }

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
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $geohash = new GeoHash();
        $current_location_hash = $geohash->encode($latitude, $longitude);
        $location_prefix = substr($current_location_hash, 0, 6);
        $neighbours = $geohash->neighbors($location_prefix);
        array_push($neighbours, $location_prefix);
        $locations = array();
        foreach ($neighbours as $location){
            $temp_block = Location::where('geo_hash', 'like', "{$location}%")->get();
            foreach($temp_block as $loc){
                array_push($locations, $loc);
            }
        }
        return s("ok", [
            "locations" => $locations
        ]);
    }

}