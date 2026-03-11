<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Organization Settings</h1>
                <p class="mt-1 text-sm text-gray-600">Manage your organization details and shipping providers</p>
            </div>

            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Organization Name <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                                    <input type="email" wire:model="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-500 @enderror">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" wire:model="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="gstin" class="block text-sm font-medium text-gray-700">GSTIN</label>
                                    <input type="text" wire:model="gstin" id="gstin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea wire:model="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>

                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" wire:model="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                    <input type="text" wire:model="state" id="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="pincode" class="block text-sm font-medium text-gray-700">Pincode</label>
                                    <input type="text" wire:model="pincode" id="pincode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <input type="text" wire:model="country" id="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Providers -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Providers</h3>
                            <p class="text-sm text-gray-600 mb-6">Configure shipping providers for this organization. Each organization can have its own API credentials.</p>
                            
                            <div class="space-y-6">
                                @foreach($shipping_providers as $provider)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <!-- Provider Header -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                    wire:click="toggleProvider({{ $provider->id }})"
                                                    @if($provider_configs[$provider->id]['enabled'] ?? false) checked @endif
                                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <label class="ml-3">
                                                    <span class="text-base font-medium text-gray-900">{{ $provider->display_name }}</span>
                                                    <p class="text-sm text-gray-500">{{ $provider->name }}</p>
                                                </label>
                                            </div>
                                            @if($provider_configs[$provider->id]['enabled'] ?? false)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Enabled
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Provider Configuration (shown when enabled) -->
                                        @if($provider_configs[$provider->id]['enabled'] ?? false)
                                            <div class="pl-7 space-y-4 border-t border-gray-200 pt-4">
                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <!-- API Configuration -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">API Key</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.api_key" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            placeholder="Enter API Key">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">API Secret / Password</label>
                                                        <input type="password" 
                                                            wire:model="provider_configs.{{ $provider->id }}.api_secret" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            placeholder="Enter API Secret">
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <label class="block text-sm font-medium text-gray-700">API Endpoint</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.api_endpoint" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            placeholder="{{ $provider->api_endpoint }}">
                                                    </div>

                                                    <!-- Shiprocket Specific -->
                                                    @if(strtolower($provider->name) === 'shiprocket')
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Pickup Location</label>
                                                            <input type="text" 
                                                                wire:model="provider_configs.{{ $provider->id }}.pickup_location" 
                                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                                placeholder="Primary">
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Channel ID</label>
                                                            <input type="text" 
                                                                wire:model="provider_configs.{{ $provider->id }}.channel_id" 
                                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                                placeholder="Channel ID">
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Default Courier ID</label>
                                                            <input type="text" 
                                                                wire:model="provider_configs.{{ $provider->id }}.default_courier_id" 
                                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                                placeholder="Courier ID">
                                                        </div>
                                                    @endif

                                                    <!-- Pickup Details -->
                                                    <div class="sm:col-span-2">
                                                        <h4 class="text-sm font-medium text-gray-900 mb-3">Pickup Location Details</h4>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Pickup Contact Name</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.pickup_name" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Pickup Phone</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.pickup_phone" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <label class="block text-sm font-medium text-gray-700">Pickup Address</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.pickup_address" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Pickup City</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.pickup_city" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Pickup State</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.pickup_state" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Pickup Pincode</label>
                                                        <input type="text" 
                                                            wire:model="provider_configs.{{ $provider->id }}.pickup_pincode" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>