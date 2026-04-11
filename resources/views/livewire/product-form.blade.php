<div>
    <form wire:submit.prevent="save">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Basic Information -->
            <div class="col-span-2">
                <h3 class="text-lg font-semibold mb-2">Basic Information</h3>
            </div>

            <div>
                <label class="block text-sm font-medium">SKU *</label>
                <input type="text" wire:model="sku" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('sku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Name *</label>
                <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium">Description</label>
                <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Category</label>
                <select wire:model="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">HSN Code</label>
                <input type="text" wire:model="hsn_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('hsn_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Pricing & Costs -->
            <div class="col-span-2 mt-4">
                <h3 class="text-lg font-semibold mb-2">Pricing & Costs</h3>
            </div>

            <div>
                <label class="block text-sm font-medium">Price (₹) *</label>
                <input type="number" step="0.01" wire:model="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Cost Price (₹)</label>
                <input type="number" step="0.01" wire:model="cost_price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('cost_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Stock Management -->
            <div class="col-span-2 mt-4">
                <h3 class="text-lg font-semibold mb-2">Stock Management</h3>
            </div>

            <div>
                <label class="block text-sm font-medium">Stock Quantity *</label>
                <input type="number" wire:model="stock_quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('stock_quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Low Stock Threshold *</label>
                <input type="number" wire:model="low_stock_threshold" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('low_stock_threshold') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Dimensions & Weight -->
            <div class="col-span-2 mt-4">
                <h3 class="text-lg font-semibold mb-2">Dimensions & Weight</h3>
            </div>

            <div>
                <label class="block text-sm font-medium">Weight (kg)</label>
                <input type="number" step="0.01" wire:model="weight" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('weight') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Length (cm)</label>
                <input type="number" step="0.01" wire:model="length" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('length') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Width (cm)</label>
                <input type="number" step="0.01" wire:model="width" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('width') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Height (cm)</label>
                <input type="number" step="0.01" wire:model="height" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('height') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Tax Information -->
            <div class="col-span-2 mt-4">
                <h3 class="text-lg font-semibold mb-2">Tax Information</h3>
            </div>

            <div>
                <label class="block text-sm font-medium">Tax Rate (%)</label>
                <input type="number" step="0.01" wire:model="tax_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., 18">
                @error('tax_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Tax Type</label>
                <select wire:model="tax_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="gst">GST (Single)</option>
                    <option value="cgst_sgst">CGST + SGST</option>
                    <option value="igst">IGST</option>
                </select>
                @error('tax_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Status -->
            <div class="col-span-2 mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 shadow-sm">
                    <span class="ml-2 text-sm">Product is active (visible in store)</span>
                </label>
                @error('is_active') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Reset</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ $product ? 'Update Product' : 'Create Product' }}
            </button>
        </div>
    </form>
</div>