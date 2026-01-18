<?php

namespace Modules\AdminModule\Entities;

use Illuminate\Database\Eloquent\Model;



class PlanFeatures extends Model

{

    public $table = "sub_plan_features";

    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    public $timestamps = false;

}