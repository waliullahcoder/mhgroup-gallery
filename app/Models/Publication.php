<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'image', 'cover_image', 'description', 'status', 'created_by', 'updated_by', 'deleted_by'];
}
