<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create Order</h1>
                        @if($lead)
                            <p class="mt-1 text-sm text-gray-600">Creating order for lead: {{ $lead->name }}</p>
                        @endif
                    </div>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Orders
                    </a>
                </div>
            </div>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Information -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                                
                                @if($convertingLead && $lead)
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm text-blue-700">
                                                    This lead needs to be converted to a customer first.
                                                </p>
                                                <div class="mt-3">
                                                    <button type="button" wire:click="quickConvertLead" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Convert Lead to Customer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($customer)
                                    <div class="bg-green-50 border-l-4 border-green-400 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-green-800">{{ $customer->name }}</h3>
                                                <div class="mt-2 text-sm text-green-700">
                                                    <p>Phone: {{ $customer->phone }}</p>
                                                    @if($customer->email)
                                                        <p>Email: {{ $customer->email }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                                    <button type="button" wire:click="addItem" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Item
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    @foreach($items as $index => $item)
                                        <div class="border border-gray-200 rounded-lg p-4 relative">
                                            @if(count($items) > 1)
                                                <button type="button" wire:click="removeItem({{ $index }})" class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                                <!-- Product -->
                                                <div class="md:col-span-3">
                                                    <label class="block text-sm font-medium text-gray-700">Product</label>
                                                    <select wire:model.live="items.{{ $index }}.product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('items.'.$index.'.product_id') border-red-500 @enderror">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">
                                                                {{ $product->name }} 
                                                                (₹{{ number_format($product->price, 2) }})
                                                                @if($product->tax_rate) - Tax: {{ $product->tax_rate }}% @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('items.'.$index.'.product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                                </div>

                                                <!-- Quantity -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                                    <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('items.'.$index.'.quantity') border-red-500 @enderror">
                                                    @error('items.'.$index.'.quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                                </div>

                                                <!-- Price -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Price (₹)</label>
                                                    <input type="number" wire:model.live="items.{{ $index }}.price" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('items.'.$index.'.price') border-red-500 @enderror">
                                                    @error('items.'.$index.'.price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                                </div>

                                                <!-- Discount -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Discount (₹)</label>
                                                    <input type="number" wire:model.live="items.{{ $index }}.discount" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                </div>
                                            </div>

                                            @if(isset($item['quantity']) && isset($item['price']) && isset($item['product_id']))
                                                @php
                                                    $product = \App\Models\Product::find($item['product_id']);
                                                    $itemSubtotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);
                                                    $taxRate = $product ? $product->getTaxRate() : 18;
                                                    $itemTax = ($itemSubtotal * $taxRate) / 100;
                                                    $itemTotal = $itemSubtotal + $itemTax;
                                                @endphp
                                                <div class="mt-2 text-right text-sm">
                                                    <div class="text-gray-600">Subtotal: ₹{{ number_format($itemSubtotal, 2) }}</div>
                                                    <div class="text-gray-600">Tax ({{ $taxRate }}%): ₹{{ number_format($itemTax, 2) }}</div>
                                                    <div class="font-medium text-gray-900">Item Total: ₹{{ number_format($itemTotal, 2) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Address</h3>
                                
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
                                        <textarea wire:model="shipping_address" id="shipping_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('shipping_address') border-red-500 @enderror"></textarea>
                                        @error('shipping_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="shipping_city" class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="shipping_city" id="shipping_city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('shipping_city') border-red-500 @enderror">
                                            @error('shipping_city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="shipping_state" class="block text-sm font-medium text-gray-700">State <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="shipping_state" id="shipping_state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('shipping_state') border-red-500 @enderror">
                                            @error('shipping_state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="shipping_pincode" class="block text-sm font-medium text-gray-700">Pincode <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="shipping_pincode" id="shipping_pincode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('shipping_pincode') border-red-500 @enderror">
                                            @error('shipping_pincode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Order Summary -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-medium">₹{{ number_format($subtotal, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between text-sm">
                                        {{-- <span class="text-gray-600">Tax ({{ $tax_rate }}%)</span> --}}
                                        <span class="font-medium">₹{{ number_format($tax, 2) }}</span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Shipping Cost (₹)</label>
                                        <input type="number" wire:model.live="shipping_cost" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Discount (₹)</label>
                                        <input type="number" wire:model.live="discount" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <div class="pt-3 border-t border-gray-200">
                                        <div class="flex justify-between">
                                            <span class="text-base font-medium text-gray-900">Total</span>
                                            <span class="text-xl font-bold text-indigo-600">₹{{ number_format($total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <select wire:model="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="cod">Cash on Delivery</option>
                                            <option value="prepaid">Prepaid</option>
                                            <option value="online">Online Payment</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                                        <select wire:model="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Provider -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Options</h3>
                                
                                @if($available_shipping_providers->count() > 0)
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Shipping Provider</label>
                                            <select wire:model.live="selected_shipping_provider_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">Select Provider (Optional)</option>
                                                @foreach($available_shipping_providers as $provider)
                                                    <option value="{{ $provider->id }}">{{ $provider->display_name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Choose a shipping provider to create shipment automatically</p>
                                        </div>

                                        @if($shipping_provider_selected)
                                            <div class="rounded-md bg-blue-50 p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <h3 class="text-sm font-medium text-blue-800">
                                                            {{ $shipping_provider_selected->display_name }} Selected
                                                        </h3>
                                                        <div class="mt-2 text-sm text-blue-700">
                                                            <p>Provider: {{ $shipping_provider_selected->name }}</p>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <label class="flex items-center">
                                                                <input type="checkbox" wire:model="create_shipment" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                                                <span class="ml-2 text-sm font-medium text-blue-800">
                                                                    Create shipment on {{ $shipping_provider_selected->display_name }} automatically
                                                                </span>
                                                            </label>
                                                            <p class="ml-6 mt-1 text-xs text-blue-600">
                                                                When checked, shipment will be created on {{ $shipping_provider_selected->display_name }} when order is saved
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($create_shipment)
                                                <div class="rounded-md bg-green-50 border-l-4 border-green-400 p-4">
                                                    <div class="flex">
                                                        <div class="flex-shrink-0">
                                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm text-green-700">
                                                                <strong>Shipment will be created automatically</strong><br>
                                                                Order will be synced with {{ $shipping_provider_selected->display_name }} and tracking number will be generated.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <div class="rounded-md bg-yellow-50 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-yellow-800">No Shipping Providers Configured</h3>
                                                <div class="mt-2 text-sm text-yellow-700">
                                                    <p>Please configure shipping providers in organization settings to enable automatic shipment creation.</p>
                                                    @if(auth()->user()->isAdmin())
                                                        <a href="{{ route('organization.edit') }}" class="font-medium underline text-yellow-700 hover:text-yellow-600 mt-2 inline-block">
                                                            Configure Shipping Providers
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                                <textarea wire:model="notes" id="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Add any special instructions..."></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="save">Create Order</span>
                            <span wire:loading wire:target="save">Creating Order...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>