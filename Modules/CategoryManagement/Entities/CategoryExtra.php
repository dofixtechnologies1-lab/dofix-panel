<?php

namespace Modules\CategoryManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\HasUuid;

class CategoryExtra extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'category_extras';
    protected $guarded = ['id'];

    public function scopeOfStatus($query, $status)
    {
        $query->where('status', '=', $status);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id','id');
    }

}
