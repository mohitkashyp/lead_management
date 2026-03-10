<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Create New Lead</h1>
                <p class="mt-1 text-sm text-gray-600">Add a new lead to your pipeline</p>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save">
                <div class="bg-white shadow rounded-lg">
                    <!-- Basic Information -->
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="phone" id="phone" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Alternate Phone -->
                            <div>
                                <label for="alternate_phone" class="block text-sm font-medium text-gray-700">Alternate Phone</label>
                                <input type="text" wire:model="alternate_phone" id="alternate_phone" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- Email -->
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model="email" id="email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Lead Source -->
                            <div>
                                <label for="lead_source_id" class="block text-sm font-medium text-gray-700">Lead Source <span class="text-red-500">*</span></label>
                                <select wire:model="lead_source_id" id="lead_source_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('lead_source_id') border-red-500 @enderror">
                                    <option value="">Select Source</option>
                                    @foreach($sources as $source)
                                    <option value="{{ $source->id }}">{{ $source->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('lead_source_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Lead Status -->
                            <div>
                                <label for="lead_status_id" class="block text-sm font-medium text-gray-700">Lead Status <span class="text-red-500">*</span></label>
                                <select wire:model="lead_status_id" id="lead_status_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('lead_status_id') border-red-500 @enderror">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('lead_status_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Assigned To -->
                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                                <select wire:model="assigned_to" id="assigned_to" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product Interest -->
                            <div>
                                <label for="product_interest" class="block text-sm font-medium text-gray-700">Product Interest</label>
                                <input type="text" wire:model="product_interest" id="product_interest" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- Estimated Value -->
                            <div>
                                <label for="estimated_value" class="block text-sm font-medium text-gray-700">Estimated Value (₹)</label>
                                <input type="number" step="0.01" wire:model="estimated_value" id="estimated_value" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- Next Follow-up Date -->
                            <div>
                                <label for="next_follow_up_date" class="block text-sm font-medium text-gray-700">Next Follow-up Date</label>
                                <input type="date" wire:model="next_follow_up_date" id="next_follow_up_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="px-4 py-5 sm:p-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Address -->
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea wire:model="address" id="address" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>

                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" wire:model="city" id="city" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- State -->
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                <input type="text" wire:model="state" id="state" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- Pincode -->
                            <div>
                                <label for="pincode" class="block text-sm font-medium text-gray-700">Pincode</label>
                                <input type="text" wire:model="pincode" id="pincode" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="px-4 py-5 sm:p-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Notes</h3>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea wire:model="notes" id="notes" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Add any additional information about this lead..."></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                        <a href="{{ route('leads.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="save">Save Lead</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>