<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    // Modal state
    public $showModal = false;
    public $editingProduct = null; // null = create mode, product id = edit mode

    // Form fields (same as ProductForm)
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

    // Categories for dropdown
    public $categories = [];

    // Search/filter
    public $search = '';

    protected $queryString = ['search'];

    protected function rules()
    {
        $skuUnique = $this->editingProduct 
            ? 'unique:products,sku,' . $this->editingProduct 
            : 'unique:products,sku';

        return [
            'category_id' => 'nullable|exists:product_categories,id',
            'sku' => "required|string|max:50|{$skuUnique}",
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

    public function mount()
    {
        $this->categories = ProductCategory::orderBy('name')->get();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editingProduct = null;
        $this->showModal = true;
    }

    public function openEditModal($productId)
    {
        $product = Product::forOrganization(Auth::user()->organization_id)
            ->findOrFail($productId);

        $this->editingProduct = $product->id;
        $this->fill($product->toArray());
        $this->price = (string) $product->price;
        $this->cost_price = (string) $product->cost_price;
        $this->tax_rate = (string) $product->tax_rate;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetErrorBag();
    }

    public function resetForm()
    {
        $this->reset([
            'category_id', 'sku', 'name', 'description', 'price',
            'cost_price', 'stock_quantity', 'low_stock_threshold',
            'weight', 'length', 'width', 'height', 'is_active',
            'tax_rate', 'tax_type', 'hsn_code', 'editingProduct'
        ]);
        $this->is_active = true;
        $this->tax_type = 'gst';
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

        if ($this->editingProduct) {
            $product = Product::forOrganization(Auth::user()->organization_id)
                ->findOrFail($this->editingProduct);
            $product->update($data);
            session()->flash('message', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('message', 'Product created successfully.');
        }

        $this->closeModal();
        $this->dispatch('product-saved'); // optional event
    }

    public function deleteProduct($productId)
    {
        $product = Product::forOrganization(Auth::user()->organization_id)
            ->findOrFail($productId);
        $product->delete();
        session()->flash('message', 'Product deleted successfully.');
    }

    public function render()
    {
        $products = Product::forOrganization(Auth::user()->organization_id)
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.product-list', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}