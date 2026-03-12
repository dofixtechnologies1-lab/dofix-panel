<?php

namespace Modules\BookingModule\Entities;

use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\Model; 
use Modules\BookingModule\Http\Traits\BookingTrait;
use Modules\BookingModule\Http\Traits\BookingScopes; 

class Addon extends Model
{
    use HasFactory, HasUuid, BookingTrait, BookingScopes;


    protected $table = 'addons';

 

    protected $fillable = [
        'id', 
        'booking_id',
        'service_id',
        'quantity'
    ];
 

 
}
