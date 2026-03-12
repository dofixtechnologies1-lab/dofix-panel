<?php

namespace Modules\AdminModule\Entities;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model

{

    public $table = "sub_plan";
    protected $guarded = [];
    protected $primaryKey = 'plan_id';
    public $timestamps = false;

    public function features()
    {
        return $this->belongsToMany(SubPlanFeatures::class,'sub_plan_to_feature','plan_id','feature_id');
    }

    public function planResources()
    {
        return $this->belongsToMany(DiabeticResource::class,'plan_resource','plan_id','resource_id');
    }
    

}