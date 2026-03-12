<?php

namespace Modules\BookingModule\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ServiceManagement\Entities\Service;

class ServiceEnquiry extends Model
{
    use HasFactory;

    protected $table="service_enquiries";
    protected $primaryKey="id";
    protected $guarded=['id'];



}
