<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold text-gray-900 mb-6">
                Organization Settings
            </h1>

            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif


            <div class="bg-white shadow rounded-lg p-6 mb-6">

                <h3 class="text-lg font-medium mb-4">
                    Organization Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <input type="text" wire:model="name" placeholder="Organization Name"
                        class="rounded-md border-gray-300 shadow-sm">

                    <input type="email" wire:model="email" placeholder="Email"
                        class="rounded-md border-gray-300 shadow-sm">

                    <input type="text" wire:model="phone" placeholder="Phone"
                        class="rounded-md border-gray-300 shadow-sm">

                    <input type="text" wire:model="website" placeholder="Website"
                        class="rounded-md border-gray-300 shadow-sm">

                    <input type="text" wire:model="gstin" placeholder="GSTIN"
                        class="rounded-md border-gray-300 shadow-sm">

                </div>

            </div>


            <div class="bg-white shadow rounded-lg p-6 mb-6">

                <h3 class="text-lg font-medium mb-4">
                    Shipping Provider
                </h3>

                <select wire:model="shipping_provider_id"  wire:change="loadSecrets" class="w-full rounded-md border-gray-300 mb-4">

                    <option value="">Select Provider</option>

                    @foreach ($providers as $provider)
                        <option value="{{ $provider->id }}">
                            {{ $provider->display_name }}
                        </option>
                    @endforeach

                </select>


                @if ($shipping_provider_id)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <input type="text" wire:model="api_key" placeholder="API Key"
                            class="rounded-md border-gray-300 shadow-sm">


                        <input type="text" wire:model="pickup_name" placeholder="Pickup Name"
                            class="rounded-md border-gray-300 shadow-sm">

                        <input type="text" wire:model="pickup_phone" placeholder="Pickup Phone"
                            class="rounded-md border-gray-300 shadow-sm">

                        <input type="text" wire:model="pickup_address" placeholder="Pickup Address"
                            class="rounded-md border-gray-300 shadow-sm">

                        <input type="text" wire:model="pickup_city" placeholder="Pickup City"
                            class="rounded-md border-gray-300 shadow-sm">

                        <input type="text" wire:model="pickup_state" placeholder="Pickup State"
                            class="rounded-md border-gray-300 shadow-sm">

                        <input type="text" wire:model="pickup_pincode" placeholder="Pickup Pincode"
                            class="rounded-md border-gray-300 shadow-sm">

                    </div>
                @endif

            </div>


            <div class="flex justify-end">

                <button wire:click="save" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

                    Save Settings

                </button>

            </div>


        </div>
    </div>
</div>
