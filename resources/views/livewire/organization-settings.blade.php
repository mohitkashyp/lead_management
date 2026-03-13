<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="sm:flex sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 ">
                        Organization Settings
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Manage and Organization Details</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    {{-- <a href="{{ route('organizations.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Organization
                    </a> --}}
                    <a href="{{ route('organization.users.list') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Manage Users
                    </a>
                </div>
            </div>
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

                <select wire:model="shipping_provider_id" wire:change="loadSecrets"
                    class="w-full rounded-md border-gray-300 mb-4">

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
