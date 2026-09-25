<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSectionCategory extends Model
{
    protected $fillable = ['home_section_id', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
