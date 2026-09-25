<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'code', 'contact_person', 'email', 'phone', 'address', 'status', 'created_by', 'updated_by', 'deleted_by'];
}
