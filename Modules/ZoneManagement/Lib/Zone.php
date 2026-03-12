<?php


use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;
use Illuminate\Support\Facades\DB;


if (!function_exists('format_coordinates')) {
    function format_coordinates($object): array
    {
        $data = [];
        foreach ($object as $coordinate) {
            $data[] = (object)['lat' => $coordinate[1], 'lng' => $coordinate[0]];
        }
        return $data;
    }
}

// if (!function_exists('formatCoordinates')) {
//     function formatCoordinates($coordinates): array
//     {   
//         $object = json_decode($coordinates[0]->toJson(),true)['coordinates'];
//         $data = [];
//         foreach ($object as $coordinate) {
//             $data[] = (object)['latitude' => $coordinate[0], 'longitude' => $coordinate[1]];
//         }
//         return $data;
//     }
// }




if (!function_exists('formatCoordinates')) {
    function formatCoordinates($coordinates): array
    {   
        if (is_string($coordinates)) {
            // Convert WKB to GeoJSON
            $geoJson = DB::select("SELECT ST_AsGeoJSON(ST_GeomFromWKB(?)) AS geojson", [$coordinates]);
            dd($geoJson);
            if (!empty($geoJson) && isset($geoJson[0]->geojson)) {
                $object = json_decode($geoJson[0]->geojson, true);
                $data = [];

                // Polygon coordinates are nested inside another array
                if (isset($object['coordinates'][0])) {
                    foreach ($object['coordinates'][0] as $coordinate) {
                        $data[] = (object)[
                            'latitude' => $coordinate[1], // GeoJSON stores [longitude, latitude]
                            'longitude' => $coordinate[0]
                        ];
                    }
                }
                return $data;
            }
        }

        return [];
    }
}

