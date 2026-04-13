<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">

                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Edit Lead
                    </h1>

                    <p class="text-sm text-gray-600">
                        Update lead information
                    </p>
                </div>

                <a href="{{ route('leads.show', $lead->id) }}"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-100">
                    Back
                </a>

            </div>


            <div class="bg-white shadow rounded-lg">

                <form wire:submit.prevent="updateLead">

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Name
                            </label>

                            <input type="text" wire:model="name"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Phone
                            </label>

                            <input type="text" wire:model="phone"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email
                            </label>

                            <input type="email" wire:model="email"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>


                        <!-- Source -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Source
                            </label>

                            <select wire:model="lead_source_id" class="w-full border-gray-300 rounded-md shadow-sm">

                                <option value="">Select Source</option>

                                @foreach ($sources as $source)
                                    <option value="{{ $source->id }}">
                                        {{ $source->display_name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>
                        <!-- Address Section -->
                        <div class="md:col-span-2 border-t pt-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                                Address Details
                            </h2>
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Address
                            </label>

                            <textarea wire:model="address" rows="3" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                City
                            </label>

                            <input type="text" wire:model="city" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- State -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State
                            </label>

                            <input type="text" wire:model="state"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Pincode -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pincode
                            </label>

                            <input type="text" wire:model="pincode"
                                class="w-full border-gray-300 rounded-md shadow-sm">

                            @error('pincode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>

                            <select wire:model="status_id" class="w-full border-gray-300 rounded-md shadow-sm">

                                <option value="">Select Status</option>

                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">
                                        {{ $status->display_name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <!-- Assigned To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Assigned To
                            </label>

                            <select wire:model="assigned_to" class="w-full border-gray-300 rounded-md shadow-sm">

                                <option value="">Unassigned</option>

                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">
                                        {{ $agent->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <!-- Follow Up -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Next Follow Up
                            </label>

                            <input type="date" wire:model="next_follow_up_date"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>


                        <!-- Notes -->
                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notes
                            </label>

                            <textarea wire:model="notes" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>

                        </div>

                    </div>


                    <!-- Buttons -->

                    <div class="px-6 py-4 border-t flex justify-end gap-3">

                        <a href="{{ route('leads.show', $lead->id) }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm">
                            Cancel
                        </a>

                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Update Lead
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
