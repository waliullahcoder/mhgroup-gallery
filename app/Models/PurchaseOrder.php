<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    // Mass assignable fields
    protected $fillable = [
        'po_number',
        'store_id',
        'vendor_id',
        'order_date',
        'expected_date',
        'total_amount',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'due_amount',
        'payment_type',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // Append formatted dates to JSON automatically
    protected $appends = ['orderDate', 'expectedDate'];

    /**
     * Booted method to handle created_by, updated_by, deleted_by, and soft deletes
     */
    protected static function booted()
    {
        // Automatically set created_by and updated_by
        static::creating(function ($order) {
            $order->created_by = Auth::id();
            $order->updated_by = Auth::id();
        });

        static::updating(function ($order) {
            $order->updated_by = Auth::id();
        });

        // Handle soft deletes for children and set deleted_by
        static::deleting(function ($order) {
            if (! $order->isForceDeleting()) {
                // Soft delete items and set deleted_by
                $order->items()->update(['deleted_by' => Auth::id()]);
                $order->items()->delete();

                // Set deleted_by on parent
                $order->deleted_by = Auth::id();
                $order->save();
            }
        });

        // Restore children and clear deleted_by
        static::restoring(function ($order) {
            $order->items()->withTrashed()->update(['deleted_by' => null]);
            $order->items()->restore();

            $order->deleted_by = null;
        });
    }

    /**
     * Relationships
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function purchaseReceipts()
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    // Track users
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Accessors for formatted dates
     */
    public function getOrderDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getExpectedDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
}