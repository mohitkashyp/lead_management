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

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    // Display Helpers
    public function getFormattedPrice(): string
    {
        return '₹' . number_format($this->price, 2);
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
        } elseif ($this->stock_quantity <= 5) { // You can make this configurable
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
}