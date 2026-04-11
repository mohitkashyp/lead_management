<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'organization_id',
        'product_category_id',
        'sku',
        'name',
        'description',
        'price',
        'cost',
        'stock_quantity',
        'weight',
        'length',
        'width',
        'height',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
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

    public function scopeLowStock($query, $threshold = 5)
    {
        return $query->where('stock_quantity', '<=', $threshold);
    }

    // Stock Management
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return !$this->isOutOfStock() && $this->stock_quantity <= $threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    // Tax Calculations
    /**
     * Get tax rate for product
     * Note: This requires 'tax_rate' column in products table
     * To add: php artisan make:migration add_tax_fields_to_products_table
     * 
     * Schema::table('products', function (Blueprint $table) {
     *     $table->decimal('tax_rate', 5, 2)->default(0)->after('price');
     *     $table->enum('tax_type', ['gst', 'cgst_sgst', 'igst'])->default('gst')->after('tax_rate');
     *     $table->string('hsn_code', 20)->nullable()->after('tax_type');
     * });
     */
    public function getTaxRate(): float
    {
        // Check if tax_rate column exists and has value
        if ($this->hasAttribute('tax_rate') && $this->tax_rate !== null) {
            return (float) $this->tax_rate;
        }
        
        // Default 18% GST if tax_rate column doesn't exist or is null
        return 18.0;
    }

    /**
     * Get tax type for product
     * Note: This requires 'tax_type' column in products table
     */
    public function getTaxType(): string
    {
        // Check if tax_type column exists and has value
        if ($this->hasAttribute('tax_type') && $this->tax_type !== null) {
            return $this->tax_type;
        }
        
        // Default 'gst' if tax_type column doesn't exist
        return 'gst';
    }

    /**
     * Get HSN code for product
     * Note: This requires 'hsn_code' column in products table
     */
    public function getHsnCode(): ?string
    {
        // Check if hsn_code column exists
        if ($this->hasAttribute('hsn_code')) {
            return $this->hsn_code;
        }
        
        return null;
    }

    /**
     * Calculate tax amount for a given price
     */
    public function calculateTax(?float $amount = null): float
    {
        $price = $amount ?? $this->price;
        $taxRate = $this->getTaxRate();
        
        return ($price * $taxRate) / 100;
    }

    /**
     * Get price including tax
     */
    public function getPriceWithTax(): float
    {
        return $this->price + $this->calculateTax();
    }

    /**
     * Get detailed tax breakdown
     */
    public function getTaxBreakdown(?float $amount = null): array
    {
        $price = $amount ?? $this->price;
        $taxType = $this->getTaxType();
        $taxRate = $this->getTaxRate();
        $totalTax = ($price * $taxRate) / 100;

        if ($taxType === 'cgst_sgst') {
            // Split equally between CGST and SGST (for intra-state)
            return [
                'type' => 'cgst_sgst',
                'cgst_rate' => $taxRate / 2,
                'sgst_rate' => $taxRate / 2,
                'cgst_amount' => $totalTax / 2,
                'sgst_amount' => $totalTax / 2,
                'total_tax' => $totalTax,
                'taxable_amount' => $price,
                'total_amount' => $price + $totalTax,
            ];
        } elseif ($taxType === 'igst') {
            // Interstate GST
            return [
                'type' => 'igst',
                'igst_rate' => $taxRate,
                'igst_amount' => $totalTax,
                'total_tax' => $totalTax,
                'taxable_amount' => $price,
                'total_amount' => $price + $totalTax,
            ];
        } else {
            // Default GST
            return [
                'type' => 'gst',
                'gst_rate' => $taxRate,
                'gst_amount' => $totalTax,
                'total_tax' => $totalTax,
                'taxable_amount' => $price,
                'total_amount' => $price + $totalTax,
            ];
        }
    }

    /**
     * Check if product has tax configuration
     */
    public function hasTaxConfig(): bool
    {
        return $this->hasAttribute('tax_rate') && $this->tax_rate !== null && $this->tax_rate > 0;
    }

    // Display Helpers
    public function getFormattedPrice(): string
    {
        return '₹' . number_format($this->price, 2);
    }

    public function getFormattedPriceWithTax(): string
    {
        return '₹' . number_format($this->getPriceWithTax(), 2);
    }

    public function getFormattedCost(): ?string
    {
        if ($this->cost) {
            return '₹' . number_format($this->cost, 2);
        }
        return null;
    }

    public function getProfitMargin(): ?float
    {
        if ($this->cost && $this->cost > 0) {
            return (($this->price - $this->cost) / $this->cost) * 100;
        }
        return null;
    }

    public function getFormattedProfitMargin(): ?string
    {
        $margin = $this->getProfitMargin();
        if ($margin !== null) {
            return number_format($margin, 2) . '%';
        }
        return null;
    }

    public function getStockStatus(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusLabel(): string
    {
        return match($this->getStockStatus()) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
        };
    }

    public function getStockStatusColor(): string
    {
        return match($this->getStockStatus()) {
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'in_stock' => 'green',
        };
    }

    // Dimensions helper
    public function getDimensions(): ?string
    {
        if ($this->length && $this->width && $this->height) {
            return sprintf('%s x %s x %s cm', $this->length, $this->width, $this->height);
        }
        return null;
    }

    public function getFormattedDimensions(): string
    {
        return $this->getDimensions() ?? 'N/A';
    }

    public function getFormattedWeight(): string
    {
        if ($this->weight) {
            return number_format($this->weight, 2) . ' kg';
        }
        return 'N/A';
    }

    // Helper to check if attribute exists (for dynamic columns)
    protected function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getAttributes());
    }
}