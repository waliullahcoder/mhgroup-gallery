<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use SoftDeletes;
    protected $fillable = ['purchase_order_id', 'product_id', 'product_variant_id', 'quantity', 'received_quantity', 'unit_price', 'discount_amount', 'tax_amount', 'total_amount', 'notes', 'created_by', 'updated_by', 'deleted_by'];

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->created_by = Auth::id();
            $item->updated_by = Auth::id();
        });

        static::updating(function ($item) {
            $item->updated_by = Auth::id();
        });
    }

    /**
     * The purchase order this item belongs to
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * The product this item refers to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The product variant (if applicable)
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * User who created this item
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated this item
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * User who soft deleted this item
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
