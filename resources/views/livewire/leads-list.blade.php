<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="sm:flex sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Leads</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage and track all your leads</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('leads.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Lead
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Search -->
                        <div class="col-span-1 sm:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" wire:model.live.debounce.300ms="search" id="search"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Search by name, email, phone...">
                        </div>

                        <!-- Source Filter -->
                        <div>
                            <label for="filterSource"
                                class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                            <select wire:model.live="filterSource" id="filterSource"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">All Sources</option>
                                @foreach ($sources as $source)
                                    <option value="{{ $source->id }}">{{ $source->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="filterStatus"
                                class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.live="filterStatus" id="filterStatus"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">All Statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assigned To Filter -->
                        <div>
                            <label for="filterAssignedTo" class="block text-sm font-medium text-gray-700 mb-1">Assigned
                                To</label>
                            <select wire:model.live="filterAssignedTo" id="filterAssignedTo"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">All Agents</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <div class="col-span-1 sm:col-span-2 lg:col-span-1 flex items-end">
                            <button wire:click="clearFilters" type="button"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leads Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                               
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    wire:click="sortBy('name')">
                                    <div class="flex items-center">
                                        Name
                                        @if ($sortField === 'name')
                                            <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                @if ($sortDirection === 'asc')
                                                    <path d="M5 10l5-5 5 5H5z" />
                                                @else
                                                    <path d="M5 10l5 5 5-5H5z" />
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Contact
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Source
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                    Assigned To
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell cursor-pointer"
                                    wire:click="sortBy('next_follow_up_date')">
                                    <div class="flex items-center">
                                        Next Follow-up
                                        @if ($sortField === 'next_follow_up_date')
                                            <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                @if ($sortDirection === 'asc')
                                                    <path d="M5 10l5-5 5 5H5z" />
                                                @else
                                                    <path d="M5 10l5 5 5-5H5z" />
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($leads as $lead)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center gap-2">

                                            <button wire:click="openFollowup({{ $lead->id }})"
                                                class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Follow Up
                                            </button>

                                            <a href="{{ route('leads.show', $lead->id) }}"
                                                class="px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                                View
                                            </a>

                                            {{-- <a href="{{ route('leads.edit', $lead->id) }}"
                                                class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                                                Edit
                                            </a> --}}
                                            @if(auth()->user()->isAdmin())
                                            <button wire:click="deleteLead({{ $lead->id }})"
                                                wire:confirm="Are you sure you want to delete this lead?"
                                                class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                                Delete
                                            </button>
                                                
                                            @endif

                                        </div>
                                    </td>
                                   
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $lead->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-gray-900">{{ $lead->phone }}</div>
                                        @if ($lead->email)
                                            <div class="text-sm text-gray-500">{{ $lead->email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            style="background-color: {{ $lead->source->color }}20; color: {{ $lead->source->color }}">
                                            {{ $lead->source->display_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            style="background-color: {{ $lead->status->color }}20; color: {{ $lead->status->color }}">
                                            {{ $lead->status->display_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        {{ $lead->assignedTo?->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                                        @if ($lead->next_follow_up_date)
                                            <span
                                                class="{{ $lead->next_follow_up_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $lead->next_follow_up_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-1">No leads found</p>
                                            <p class="text-gray-500">Get started by creating a new lead.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $leads->links() }}
                </div>
            </div>
        </div>
    </div>
    @if ($selectedLead)
        <div class="fixed bottom-6 right-6 w-96 bg-white border border-gray-200 rounded-lg shadow-lg p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Log Follow Up
            </h3>


            <!-- Toggle -->

            <div class="flex gap-2 mb-4">

                <button wire:click="$set('activityType','call')"
                    class="px-3 py-1 text-sm rounded-md border
@if ($activityType == 'call') bg-indigo-600 text-white border-indigo-600
@else
bg-white text-gray-700 border-gray-300 @endif">
                    Call
                </button>

                <button wire:click="$set('activityType','note')"
                    class="px-3 py-1 text-sm rounded-md border
@if ($activityType == 'note') bg-indigo-600 text-white border-indigo-600
@else
bg-white text-gray-700 border-gray-300 @endif">
                    Note
                </button>

            </div>


            <!-- Description -->

            <label class="block text-sm font-medium text-gray-700">
                Description
            </label>

            <textarea wire:model="activityNote" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Write summary...">
</textarea>


            <!-- Followup Date -->

            <label class="block text-sm font-medium text-gray-700 mt-4">
                Next Followup Date
            </label>

            <input type="date" wire:model="followupDate"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">


            <!-- Buttons -->

            <div class="flex justify-end gap-2 mt-5">

                <button wire:click="$set('selectedLead', null)"
                    class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
                    Cancel
                </button>

                <button wire:click="saveFollowup"
                    class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Save
                </button>

            </div>

        </div>
    @endif
</div>
