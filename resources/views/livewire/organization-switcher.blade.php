<!-- Simple Mobile-Friendly Version -->
<div class="relative" x-data="{ open: false }">
    <!-- Mobile Button -->
    <button 
        @click="open = !open"
        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span class="max-w-[120px] truncate">{{ $currentOrganization?->name ?? 'Select' }}</span>
        <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown -->
    <div 
        x-show="open"
        x-transition
        @click.away="open = false"
        class="absolute right-0 z-50 mt-2 w-64 sm:w-72 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
        style="display: none;"
    >
        <div class="p-2">
            @forelse($organizations as $org)
                <button
                    wire:click="switchOrganization({{ $org->id }})"
                    @click="open = false"
                    class="w-full px-3 py-2 text-left rounded-md hover:bg-gray-100 {{ $currentOrganization && $currentOrganization->id === $org->id ? 'bg-indigo-50' : '' }}"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $org->name }}</p>
                            @php
                                $role = auth()->user()->getRoleInOrganization($org->id);
                            @endphp
                            <p class="text-xs text-gray-500">{{ $role?->display_name }}</p>
                        </div>
                        @if($currentOrganization && $currentOrganization->id === $org->id)
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                </button>
            @empty
                <p class="px-3 py-4 text-sm text-gray-500 text-center">No organizations</p>
            @endforelse
        </div>
    </div>
</div>