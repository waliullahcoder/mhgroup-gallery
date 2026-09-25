<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = ['title', 'type', 'product_type', 'category_id', 'serial', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsTo(HomeSectionCategory::class, 'home_section_id');
    }
}
