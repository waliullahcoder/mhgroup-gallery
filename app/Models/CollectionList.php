<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionList extends Model
{
    protected $fillable = ['collection_id', 'sales_id', 'amount'];

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
