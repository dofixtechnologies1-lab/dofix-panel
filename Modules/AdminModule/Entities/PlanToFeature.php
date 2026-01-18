<?php

namespace Modules\AdminModule\Entities;

use Illuminate\Database\Eloquent\Model;

class PlanToFeature extends Model
{

    public $table = "sub_plan_to_feature";
    protected $guarded = [];
    public $timestamps = false;

}