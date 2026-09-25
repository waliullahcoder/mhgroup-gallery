<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type',
        'image',
        'description',
        'status',
        'position',
        'serial',
        'url',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /* ================================
     | SCOPES
     ================================= */

    /**
     * Only root categories (parent_id = null)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /* ================================
     | RELATIONSHIPS
     ================================= */

    /**
     * Parent category
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
    public function parents()
    {
        return $this->belongsToMany(Category::class, 'category_subcategory', 'subcategory_id', 'parent_id');
    }

    /**
     * Child categories
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->active()
            ->orderBy('id', 'asc');
    }

     // Products under sub-category
    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'category_id');
    // }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id')
                    ->with('variants')
                    ->where('status', 1); // only active products
    }

    /**
     * Recursive children (children of children)
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /* ================================
     | HELPERS
     ================================= */

    /**
     * Get all descendant category IDs (recursive)
     */
    public function descendantIds(): array
    {
        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids   = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    /**
     * Check if category is root
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Category URL
     */
    public function getUrlAttribute()
    {
        if (!empty($this->attributes['url'])) {
            return $this->attributes['url'];
        }

        if (request()->is('admin/*')) {
            return '#';
        }

        return route('category.index', [$this->id, $this->slug]);
    }
    public function subcategories()
    {
        return $this->belongsToMany(Category::class, 'category_subcategory', 'parent_id', 'subcategory_id');
    }

}
