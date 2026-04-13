<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Previous/Next Navigation -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('leads.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Back to Leads
                            </a>

                        </div>

                        <div class="border-l border-gray-300 h-8"></div>

                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $lead->name }}</h1>
                            <p class="mt-1 text-sm text-gray-600">Lead #{{ $lead->lead_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        
                        @if ($previousLeadId)
                            <a href="{{ route('leads.show', $previousLeadId) }}"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                title="Previous Lead">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @else
                            <button disabled
                                class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed"
                                title="No Previous Lead">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @endif

                        @if ($nextLeadId)
                            <a href="{{ route('leads.show', $nextLeadId) }}"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                title="Next Lead">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <button disabled
                                class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed"
                                title="No Next Lead">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif
                        <a href="{{ route('leads.edit', $lead->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Lead Details -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Lead Information</h3>

                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Phone</dt>

                                    <dd>
                                        <!-- Phone Number -->
                                        <div class="text-lg font-semibold text-gray-900 mb-3">
                                            {{ $lead->phone }}
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex gap-2">

                                            <!-- Copy -->
                                            <button onclick="copyPhone('{{ $lead->phone }}')"
                                                class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 active:scale-95 transition">
                                                📋 Copy
                                            </button>

                                            <!-- Call -->
                                            <a wire:click="quickAddCall" href="tel:{{ $lead->phone }}"
                                                class="flex items-center gap-2 px-3 py-2 rounded-lg border border-green-500 text-green-600 text-sm font-medium hover:bg-green-50 active:scale-95 transition">
                                                📞 Call
                                            </a>

                                            <!-- WhatsApp -->
                                            <a wire:click="quickAddWhastapp" href="https://wa.me/91{{ $lead->phone }}"
                                                target="_blank"
                                                class="flex items-center gap-2 px-3 py-2 rounded-lg border border-green-600 text-green-700 text-sm font-medium hover:bg-green-100 active:scale-95 transition">
                                                💬 WhatsApp
                                            </a>

                                        </div>
                                    </dd>
                                </div>

                                @if ($lead->email)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $lead->email }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Source</dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            style="background-color: {{ $lead->source->color }}20; color: {{ $lead->source->color }}">
                                            {{ $lead->source->display_name }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            style="background-color: {{ $lead->status->color }}20; color: {{ $lead->status->color }}">
                                            {{ $lead->status->display_name }}
                                        </span>
                                    </dd>
                                </div>

                                @if ($lead->assignedTo)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $lead->assignedTo->name }}</dd>
                                    </div>
                                @endif

                                @if ($lead->product_interest)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Product Interest</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $lead->product_interest }}</dd>
                                    </div>
                                @endif

                                @if ($lead->estimated_value)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estimated Value</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            ₹{{ number_format($lead->estimated_value, 2) }}</dd>
                                    </div>
                                @endif

                                @if ($lead->next_follow_up_date)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Next Follow-up</dt>
                                        <dd
                                            class="mt-1 text-sm {{ $lead->next_follow_up_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                            {{ $lead->next_follow_up_date->format('M d, Y') }}
                                            @if ($lead->next_follow_up_date->isPast())
                                                (Overdue)
                                            @endif
                                        </dd>
                                    </div>
                                @endif

                                @if ($lead->address || $lead->city || $lead->state)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ implode(', ', array_filter([$lead->address, $lead->city, $lead->state, $lead->pincode])) }}
                                        </dd>
                                    </div>
                                @endif

                                @if ($lead->notes)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $lead->notes }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Activity Timeline</h3>
                                <button wire:click="toggleActivityForm" type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Activity
                                </button>
                            </div>

                            <!-- Activity Form -->
                            @if ($showActivityForm)
                                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Activity
                                                    Type</label>
                                                <select wire:model="activity_type"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="call">Call</option>
                                                    <option value="email">Email</option>
                                                    <option value="meeting">Meeting</option>
                                                    <option value="note">Note</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Activity
                                                    Date</label>
                                                <input type="datetime-local" wire:model="activity_date"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Subject</label>
                                            <input type="text" wire:model="subject"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('subject') border-red-500 @enderror">
                                            @error('subject')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea wire:model="description" rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Next Follow-up Date
                                                (Optional)</label>
                                            <input type="date" wire:model="next_follow_up_date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <div class="flex justify-end gap-3">
                                            <button type="button" wire:click="toggleActivityForm"
                                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                Cancel
                                            </button>
                                            <button type="button" wire:click="saveActivity"
                                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                                Save Activity
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Quick Actions -->
                            <div class="mb-4 flex gap-2">
                                <button wire:click="quickAddCall" type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Log Call
                                </button>
                                <button wire:click="quickAddEmail" type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Log Email
                                </button>
                            </div>

                            <!-- Activities List -->
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @forelse($activities as $index => $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if (!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                        aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                        {{ $activity->activity_type === 'call' ? 'bg-blue-500' : '' }}
                                                        {{ $activity->activity_type === 'email' ? 'bg-green-500' : '' }}
                                                        {{ $activity->activity_type === 'meeting' ? 'bg-purple-500' : '' }}
                                                        {{ $activity->activity_type === 'note' ? 'bg-yellow-500' : '' }}
                                                        {{ $activity->activity_type === 'status_change' ? 'bg-indigo-500' : '' }}
                                                        {{ $activity->activity_type === 'other' ? 'bg-gray-500' : '' }}
                                                    ">
                                                            @if ($activity->activity_type === 'call')
                                                                <svg class="h-5 w-5 text-white" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                </svg>
                                                            @elseif($activity->activity_type === 'email')
                                                                <svg class="h-5 w-5 text-white" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                </svg>
                                                            @elseif($activity->activity_type === 'meeting')
                                                                <svg class="h-5 w-5 text-white" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                </svg>
                                                            @else
                                                                <svg class="h-5 w-5 text-white" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                                </svg>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $activity->subject }}</p>
                                                            @if ($activity->description)
                                                                <p class="mt-0.5 text-sm text-gray-500">
                                                                    {{ $activity->description }}</p>
                                                            @endif
                                                            <p class="mt-2 text-xs text-gray-500">
                                                                by {{ $activity->user->name }}
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            <time
                                                                datetime="{{ $activity->activity_date }}">{{ $activity->activity_date->format('M d, Y') }}</time>
                                                            <p class="text-xs">
                                                                {{ $activity->activity_date->format('h:i A') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-center py-8 text-gray-500">
                                            No activities yet. Add your first activity!
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>

                            <div class="space-y-3">
                                @if (!$lead->customer_id)
                                    <button wire:click="convertToCustomer" type="button"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Convert to Customer
                                    </button>
                                @else
                                    <div class="bg-green-50 border border-green-200 rounded-md p-3 text-center">
                                        <svg class="mx-auto h-6 w-6 text-green-600 mb-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm font-medium text-green-900">Converted to Customer</p>
                                        <p class="text-xs text-green-700 mt-1">{{ $lead->customer->name }}</p>
                                    </div>
                                @endif

                                <a href="{{ route('orders.create', $lead->id) }}"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Create Order
                                </a>

                                <a href="{{ route('leads.edit', $lead->id) }}"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Lead
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>

                            <div class="space-y-3">
                                <select wire:model="new_status_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->display_name }}</option>
                                    @endforeach
                                </select>

                                <button wire:click="updateStatus" type="button"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Update Status
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Meta -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Lead Details</h3>

                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $lead->created_at->format('M d, Y h:i A') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->updated_at->diffForHumans() }}
                                    </dd>
                                </div>

                                @if ($lead->converted_at)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500">Converted On</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $lead->converted_at->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function copyPhone(phone) {
        navigator.clipboard.writeText(phone);
    }
</script>
