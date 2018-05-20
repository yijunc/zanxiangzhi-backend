<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 5/20/18
 * Time: 10:18 AM
 */

require_once 'GeoHash.php';
$geohash = new Junning\GeoHash\GeoHash;
//得到这点的hash值
$hash = $geohash->encode(39.98123848, 116.30683690);
//取前缀，前缀约长范围越小
$prefix = substr($hash, 0, 6);
//取出相邻八个区域
$neighbors = $geohash->neighbors($prefix);
array_push($neighbors, $prefix);
print_r($neighbors);