<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'description', 'status', 'created_by', 'updated_by', 'deleted_by'];
}
