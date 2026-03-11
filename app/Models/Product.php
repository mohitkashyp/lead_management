<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'organization_id',
        'category_id',
        'sku',
        'name',
        'description',
        'price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'length',
        'width',
        'height',
        'is_active',
        'tax_rate',
        'tax_type',
        'hsn_code',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    // Stock Management
    public function decreaseStock($quantity)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function increaseStock($quantity)
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock()
    {
        return $this->stock_quantity <= 0;
    }

    // Tax Calculations
    public function getTaxRate()
    {
        return $this->tax_rate ?? 18; // Default 18% GST
    }

    public function getTaxType()
    {
        return $this->tax_type ?? 'gst'; // gst, cgst_sgst, igst
    }

    public function calculateTax($amount)
    {
        $taxRate = $this->getTaxRate();
        return ($amount * $taxRate) / 100;
    }

    public function getPriceWithTax()
    {
        return $this->price + $this->calculateTax($this->price);
    }

    public function getTaxBreakdown($amount)
    {
        $taxType = $this->getTaxType();
        $taxRate = $this->getTaxRate();
        $totalTax = ($amount * $taxRate) / 100;

        if ($taxType === 'cgst_sgst') {
            // Split equally between CGST and SGST
            return [
                'type' => 'cgst_sgst',
                'cgst_rate' => $taxRate / 2,
                'sgst_rate' => $taxRate / 2,
                'cgst_amount' => $totalTax / 2,
                'sgst_amount' => $totalTax / 2,
                'total_tax' => $totalTax,
            ];
        } elseif ($taxType === 'igst') {
            // Interstate GST
            return [
                'type' => 'igst',
                'igst_rate' => $taxRate,
                'igst_amount' => $totalTax,
                'total_tax' => $totalTax,
            ];
        } else {
            // Default GST
            return [
                'type' => 'gst',
                'gst_rate' => $taxRate,
                'gst_amount' => $totalTax,
                'total_tax' => $totalTax,
            ];
        }
    }

    // Display Helpers
    public function getFormattedPrice()
    {
        return '₹' . number_format($this->price, 2);
    }

    public function getFormattedPriceWithTax()
    {
        return '₹' . number_format($this->getPriceWithTax(), 2);
    }

    public function getStockStatus()
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusLabel()
    {
        $status = $this->getStockStatus();
        
        return match($status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
        };
    }

    public function getStockStatusColor()
    {
        $status = $this->getStockStatus();
        
        return match($status) {
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'in_stock' => 'green',
        };
    }
}