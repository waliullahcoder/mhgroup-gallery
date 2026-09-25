<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAuthor extends Model
{
    protected $fillable = ['product_id', 'author_id'];

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }
}
