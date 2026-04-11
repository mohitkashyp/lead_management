<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductForm extends Component
{
    public ?Product $product = null;

    // Form fields
    public $category_id = '';
    public $sku = '';
    public $name = '';
    public $description = '';
    public $price = '';
    public $cost_price = '';
    public $stock_quantity = '';
    public $low_stock_threshold = '';
    public $weight = '';
    public $length = '';
    public $width = '';
    public $height = '';
    public $is_active = true;
    public $tax_rate = '';
    public $tax_type = 'gst';
    public $hsn_code = '';

    public $categories = [];

    protected function rules()
    {
        return [
            'category_id' => 'nullable|exists:product_categories,id',
            'sku' => 'required|string|max:50|unique:products,sku,' . ($this->product?->id ?? 'NULL'),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_type' => 'required|in:gst,cgst_sgst,igst',
            'hsn_code' => 'nullable|string|max:20',
        ];
    }

    public function mount($productId = null)
    {
        // Load categories for dropdown
        $this->categories = ProductCategory::orderBy('name')->get();

        if ($productId) {
            $product = Product::forOrganization(Auth::user()->organization_id)
                ->findOrFail($productId);

            $this->product = $product;
            $this->fill($product->toArray());
            // Convert decimal fields to string to avoid Livewire type issues
            $this->price = (string) $product->price;
            $this->cost_price = (string) $product->cost_price;
            $this->tax_rate = (string) $product->tax_rate;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'organization_id' => Auth::user()->organization_id,
            'category_id' => $this->category_id ?: null,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'cost_price' => $this->cost_price ?: null,
            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'weight' => $this->weight ?: null,
            'length' => $this->length ?: null,
            'width' => $this->width ?: null,
            'height' => $this->height ?: null,
            'is_active' => $this->is_active,
            'tax_rate' => $this->tax_rate ?: null,
            'tax_type' => $this->tax_type,
            'hsn_code' => $this->hsn_code ?: null,
        ];

        if ($this->product) {
            $this->product->update($data);
            session()->flash('message', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('message', 'Product created successfully.');
            $this->resetForm();
        }

        return redirect()->route('products.index'); // adjust route as needed
    }

    public function resetForm()
    {
        $this->reset([
            'category_id', 'sku', 'name', 'description', 'price',
            'cost_price', 'stock_quantity', 'low_stock_threshold',
            'weight', 'length', 'width', 'height', 'is_active',
            'tax_rate', 'tax_type', 'hsn_code'
        ]);
        $this->is_active = true;
        $this->tax_type = 'gst';
    }

    public function render()
    {
        return view('livewire.product-form')->layout('layouts.app');
    }
}