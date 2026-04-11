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

    // Form fields (matching migration)
    public $category_id = '';
    public $sku = '';
    public $name = '';
    public $description = '';
    public $price = '';
    public $cost = ''; // Changed from cost_price to cost
    public $stock_quantity = '';
    public $weight = '';
    public $length = '';
    public $width = '';
    public $height = '';
    public $image = '';
    public $is_active = true;

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
            'sku' => "required|string|max:255|{$skuUnique}",
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_active' => 'boolean',
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
        $this->category_id = $product->product_category_id;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = (string) $product->price;
        $this->cost = $product->cost ? (string) $product->cost : '';
        $this->stock_quantity = (string) $product->stock_quantity;
        $this->weight = $product->weight ? (string) $product->weight : '';
        $this->length = $product->length ? (string) $product->length : '';
        $this->width = $product->width ? (string) $product->width : '';
        $this->height = $product->height ? (string) $product->height : '';
        $this->image = $product->image ?? '';
        $this->is_active = $product->is_active;
        
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
            'cost', 'stock_quantity', 'weight', 'length', 'width', 
            'height', 'image', 'is_active', 'editingProduct'
        ]);
        $this->is_active = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'organization_id' => Auth::user()->organization_id,
            'product_category_id' => $this->category_id ?: null,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'cost' => $this->cost ?: null,
            'stock_quantity' => $this->stock_quantity,
            'weight' => $this->weight ?: null,
            'length' => $this->length ?: null,
            'width' => $this->width ?: null,
            'height' => $this->height ?: null,
            'image' => $this->image ?: null,
            'is_active' => $this->is_active,
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
        $this->dispatch('product-saved');
    }

    public function deleteProduct($productId)
    {
        $product = Product::forOrganization(Auth::user()->organization_id)
            ->findOrFail($productId);
        $product->delete();
        session()->flash('message', 'Product deleted successfully.');
    }

    public function toggleStatus($productId)
    {
        $product = Product::forOrganization(Auth::user()->organization_id)
            ->findOrFail($productId);
        $product->is_active = !$product->is_active;
        $product->save();
        
        $status = $product->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Product {$status} successfully.");
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