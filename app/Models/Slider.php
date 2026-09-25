<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use SoftDeletes;
    protected $fillable = ['image', 'mobile_image', 'link', 'status', 'created_by', 'updated_by', 'deleted_by'];
}
