<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'code', 'slug', 'profit','profit_percent','required_share', 'show_dashboard', 'serial','category_id', 'uom_id', 'brand_id', 'publication_id', 'product_type', 'barcode', 'file', 'thumbnail', 'short_description', 'description', 'purchase_price', 'regular_price', 'sale_price', 'discount', 'discount_type', 'discount_start_date', 'discount_end_date', 'sku', 'meta_title', 'meta_description', 'meta_image', 'custom_barcode', 'favorite', 'trending', 'new_arrival', 'best_seller', 'status', 'created_by', 'updated_by', 'deleted_by'];
    protected $appends = ['author_names'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id')->withTimestamps();
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'product_authors', 'product_id', 'author_id')->withTimestamps();
    }
    public function productauthor()
    {
        return $this->belongsTo(ProductAuthor::class, 'product_id');
    }

    public function getAuthorNamesAttribute()
    {
        return $this->authors->pluck('name')->implode(', ');
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function tags()
    {
        return $this->hasMany(ProductTag::class, 'product_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'product_vendors', 'product_id', 'vendor_id')->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function edition()
    {
        return $this->hasOne(ProductEdition::class, 'product_id');
    }

    // All attributes assigned across its variants
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_variant_values')
            ->withPivot('attribute_value_id')
            ->withTimestamps();
    }

    public function getUniqueAttributesAttribute()
    {
        return $this->attributes()
            ->get()
            ->groupBy('id')
            ->map(function ($items) {
                $attribute = $items->first();

                // fetch distinct AttributeValue models
                $valueIds = $items->pluck('pivot.attribute_value_id')->unique();

                $attribute->values = \App\Models\AttributeValue::whereIn('id', $valueIds)
                    ->orderBy('name') // order values by name too
                    ->get(['id', 'name']);

                return $attribute;
            })
            ->sortBy('name') // order attributes by name
            ->values();
    }

    protected static function boot()
    {
        parent::boot();

        // When creating
        static::creating(function ($product) {
            if (empty($product->barcode)) {
                $product->barcode = self::generateBarcode();
            }
        });

        // When updating
        static::updating(function ($product) {
            if (empty($product->barcode)) {
                $product->barcode = self::generateBarcode();
            }
        });
    }

    /**
     * Generate a unique random barcode
     */
    protected static function generateBarcode()
    {
        $barcode = mt_rand(100000000000, 999999999999); // 12-digit
        while (self::where('barcode', $barcode)->exists()) {
            $barcode = mt_rand(100000000000, 999999999999);
        }
        return $barcode;
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
    public function invests()
    {
        return $this->hasMany(Invest::class, 'product_id');
    }
    public function getCategoryNamesAttribute()
    {
        return $this->categories->pluck('name')->implode(', ');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
