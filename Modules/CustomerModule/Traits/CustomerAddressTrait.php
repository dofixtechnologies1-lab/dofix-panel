<?php

namespace Modules\CustomerModule\Traits;

use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\UserManagement\Entities\UserAddress;
use Modules\ZoneManagement\Entities\Zone;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Log;

trait CustomerAddressTrait
{
    public function add_address($payload, $user_id, $is_guest = 0)
    {
        if (!is_array($payload)) { 
            $payload = (array) $payload;
        }
        else{
            $payload = $payload;
        }
        $point = new Point($payload["lat"], $payload["lon"]);
        // $zone = Zone::whereContains('coordinates', $point)->ofStatus(1)->latest()->first();
        // dd($point->latitude);
        $zone = Zone::whereRaw("ST_Contains(coordinates, ST_GeomFromText(?))", [
            "POINT({$point->longitude} {$point->latitude})"
        ])->ofStatus(1)->latest()->first();
        

        if ($zone) {
            $zone_id = $zone->id;
        } else {
            $zone_id = null;
        }
        
        // if($user_id != ''){
            // dd("here");
        $address = new UserAddress;
        $address->user_id = $user_id;
        $address->lat = $payload["lat"];
        $address->lon = $payload["lon"];
        $address->city = $payload["city"] ?? '';
        $address->street = $payload["street"] ?? '';
        $address->zip_code = $payload["zip_code"] ?? '';
        $address->country = $payload["country"] ?? '';
        $address->address = $payload["address"];
        $address->zone_id = $zone_id;
        $address->address_type = $payload["address_type"] ?? 'service';
        $address->contact_person_name = $payload["contact_person_name"];
        $address->contact_person_number = $payload["contact_person_number"];
        $address->address_label = $payload["address_label"];
        $address->house = $payload["house"] ?? '';
        $address->floor = $payload["floor"] ?? '';
        $address->is_guest = $is_guest;
        $address->save();
            
        // }
        
        // dd("knkd");

        return $address->id ?? null;
    }
    
    public function pay_add_address($payload, $user_id, $is_guest = 0)
    {
        $payload = json_decode($payload, true);
        // dd($payload);
        // Log::info("paylod", ['payload' => $payload]);
      
        $point = new Point($payload["lat"], $payload["lon"]);
        // $zone = Zone::whereContains('coordinates', $point)->ofStatus(1)->latest()->first();
        // dd($point);
        $zone = Zone::whereRaw("ST_Contains(coordinates, ST_GeomFromText(?))", [
            "POINT({$point->longitude} {$point->latitude})"
        ])->ofStatus(1)->latest()->first();

        if ($zone) {
            $zone_id = $zone->id;
        } else {
            $zone_id = null;
        }
        
        // if($user_id != ''){
            // dd("here");
        $address = new UserAddress;
        $address->user_id = $user_id;
        $address->lat = $payload["lat"];
        $address->lon = $payload["lon"];
        $address->city = $payload["city"] ?? '';
        $address->street = $payload["street"] ?? '';
        $address->zip_code = $payload["zip_code"] ?? '';
        $address->country = $payload["country"] ?? '';
        $address->address = $payload["address"];
        $address->zone_id = $zone_id;
        $address->address_type = $payload["address_type"] ?? 'service';
        $address->contact_person_name = $payload["contact_person_name"];
        $address->contact_person_number = $payload["contact_person_number"];
        $address->address_label = $payload["address_label"];
        $address->house = $payload["house"] ?? '';
        $address->floor = $payload["floor"] ?? '';
        $address->is_guest = $is_guest;
        $address->save();
            
        // }
        
        // dd($address->id);

        return $address->id ?? null;
    }

}
