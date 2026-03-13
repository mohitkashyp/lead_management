<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Follow-ups Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">All your follow-ups in one place</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Today</dt>
                                    <dd class="text-2xl font-semibold text-blue-600">{{ number_format($stats['today']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Overdue</dt>
                                    <dd class="text-2xl font-semibold text-red-600">{{ number_format($stats['overdue']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">This Week</dt>
                                    <dd class="text-2xl font-semibold text-green-600">{{ number_format($stats['this_week']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Next Week</dt>
                                    <dd class="text-2xl font-semibold text-purple-600">{{ number_format($stats['next_week']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="selectedDate" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" wire:model.live="selectedDate" id="selectedDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="filterAssignedTo" class="block text-sm font-medium text-gray-700">Assigned To</label>
                            <select wire:model.live="filterAssignedTo" id="filterAssignedTo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="filterType" class="block text-sm font-medium text-gray-700">Type</label>
                            <select wire:model.live="filterType" id="filterType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="all">All Follow-ups</option>
                                <option value="leads">Leads Only</option>
                                <option value="repurchase">Repurchase Only</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Complete Modal -->
            @if($quickActionLead)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Follow-up</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quick Note (Optional)</label>
                            <textarea wire:model="quick_note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Add any notes about the follow-up..."></textarea>
                        </div>

                        <div class="mt-5 flex gap-3">
                            <button type="button" wire:click="$set('quickActionLead', null)" class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveQuickAction" class="flex-1 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Mark Complete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Today's Follow-ups -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Follow-ups for {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                            <span class="text-sm font-normal text-gray-500">({{ $leadFollowups->count() + $repurchaseFollowups->count() }} total)</span>
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Lead Follow-ups -->
                            @if($filterType === 'all' || $filterType === 'leads')
                                @foreach($leadFollowups as $lead)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Lead
                                                </span>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $lead->name }}</h4>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">{{ $lead->phone }}</p>
                                            @if($lead->product_interest)
                                                <p class="mt-1 text-xs text-gray-400">Interest: {{ $lead->product_interest }}</p>
                                            @endif
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $lead->status->color }}20; color: {{ $lead->status->color }}">
                                                    {{ $lead->status->display_name }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $lead->source->color }}20; color: {{ $lead->source->color }}">
                                                    {{ $lead->source->display_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <button wire:click="quickComplete({{ $lead->id }})" class="flex-1 inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Complete
                                        </button>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" type="button" class="flex-1 inline-flex justify-center items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Snooze
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10" style="display: none;">
                                                <div class="py-1">
                                                    <button wire:click="snooze({{ $lead->id }}, 1)" @click="open = false" type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">1 day</button>
                                                    <button wire:click="snooze({{ $lead->id }}, 3)" @click="open = false" type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">3 days</button>
                                                    <button wire:click="snooze({{ $lead->id }}, 7)" @click="open = false" type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">7 days</button>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="inline-flex justify-center items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                            View
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            <!-- Repurchase Follow-ups -->
                            @if($filterType === 'all' || $filterType === 'repurchase')
                                @foreach($repurchaseFollowups as $reminder)
                                <div class="border border-orange-200 rounded-lg p-4 hover:bg-orange-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    Repurchase
                                                </span>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $reminder->customer->name }}</h4>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">{{ $reminder->customer->phone }}</p>
                                            <p class="mt-1 text-xs text-gray-600">Product: {{ $reminder->product->name }}</p>
                                            <p class="mt-1 text-xs text-gray-400">Last Purchase: {{ $reminder->last_purchase_date->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <a href="{{ route('repurchase.index') }}" class="flex-1 inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700">
                                            Manage
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            @if($leadFollowups->isEmpty() && $repurchaseFollowups->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No follow-ups for this date</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Overdue & Upcoming -->
                <div class="space-y-6">
                    <!-- Overdue Follow-ups -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-red-900 mb-4">
                                Overdue Follow-ups
                                <span class="text-sm font-normal text-red-600">({{ $overdueLeads->count() }})</span>
                            </h3>
                            
                            <div class="space-y-3">
                                @forelse($overdueLeads as $lead)
                                <div class="border-l-4 border-red-500 bg-red-50 p-3 rounded">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $lead->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $lead->phone }}</p>
                                            <p class="text-xs text-red-600 mt-1">Due: {{ $lead->next_follow_up_date->format('M d, Y') }}</p>
                                        </div>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="ml-3 text-xs text-indigo-600 hover:text-indigo-900">View</a>
                                    </div>
                                </div>
                                @empty
                                <p class="text-sm text-gray-500 text-center py-4">No overdue follow-ups! 🎉</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Follow-ups -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Upcoming (Next 7 days)
                                <span class="text-sm font-normal text-gray-500">({{ $upcomingLeads->count() }})</span>
                            </h3>
                            
                            <div class="space-y-3">
                                @forelse($upcomingLeads as $lead)
                                <div class="border-l-4 border-blue-500 bg-blue-50 p-3 rounded">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $lead->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $lead->phone }}</p>
                                            <p class="text-xs text-blue-600 mt-1">{{ $lead->next_follow_up_date->format('M d, Y') }} ({{ $lead->next_follow_up_date->diffForHumans() }})</p>
                                        </div>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="ml-3 text-xs text-indigo-600 hover:text-indigo-900">View</a>
                                    </div>
                                </div>
                                @empty
                                <p class="text-sm text-gray-500 text-center py-4">No upcoming follow-ups</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>