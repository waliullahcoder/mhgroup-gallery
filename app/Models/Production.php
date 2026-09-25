<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use SoftDeletes;
    protected $fillable = ['store_id', 'production_no', 'date', 'total_qty', 'remarks', 'is_approved', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['formattedDate'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function list()
    {
        return $this->hasMany(ProductionList::class, 'production_id');
    }

    public function getFormattedDateAttribute()
    {
        return date('d-m-Y', strtotime($this->date));
    }
}
