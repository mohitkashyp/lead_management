<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                <p class="mt-1 text-sm text-gray-600">Manage your account settings and preferences</p>
            </div>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
                <!-- Sidebar Navigation -->
                <aside class="py-6 px-2 sm:px-6 lg:col-span-3 lg:py-0 lg:px-0">
                    <nav class="space-y-1">
                        <button wire:click="$set('activeTab', 'profile')" class="@if($activeTab === 'profile') bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 @endif group border-l-4 px-3 py-2 flex items-center text-sm font-medium w-full">
                            <svg class="@if($activeTab === 'profile') text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="truncate">Profile</span>
                        </button>

                        <button wire:click="$set('activeTab', 'account')" class="@if($activeTab === 'account') bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 @endif group border-l-4 px-3 py-2 flex items-center text-sm font-medium w-full">
                            <svg class="@if($activeTab === 'account') text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">Account</span>
                        </button>

                        <button wire:click="$set('activeTab', 'security')" class="@if($activeTab === 'security') bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 @endif group border-l-4 px-3 py-2 flex items-center text-sm font-medium w-full">
                            <svg class="@if($activeTab === 'security') text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="truncate">Security</span>
                        </button>

                        <button wire:click="$set('activeTab', 'notifications')" class="@if($activeTab === 'notifications') bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 @endif group border-l-4 px-3 py-2 flex items-center text-sm font-medium w-full">
                            <svg class="@if($activeTab === 'notifications') text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="truncate">Notifications</span>
                        </button>

                        <button wire:click="$set('activeTab', 'organizations')" class="@if($activeTab === 'organizations') bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900 @endif group border-l-4 px-3 py-2 flex items-center text-sm font-medium w-full">
                            <svg class="@if($activeTab === 'organizations') text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif flex-shrink-0 -ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">Organizations</span>
                        </button>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="space-y-6 sm:px-6 lg:col-span-9 lg:px-0">
                    
                    <!-- Profile Tab -->
                    @if($activeTab === 'profile')
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-6">Profile Information</h3>
                                
                                <!-- Avatar Section -->
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($avatar)
                                            <img class="h-24 w-24 rounded-full object-cover" src="{{ Storage::url($avatar) }}" alt="{{ $name }}">
                                        @else
                                            <div class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center">
                                                <span class="text-3xl font-medium text-white">{{ substr($name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-5">
                                        <div class="flex items-center gap-3">
                                            <label for="new_avatar" class="cursor-pointer px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                Upload Photo
                                            </label>
                                            <input type="file" wire:model="new_avatar" id="new_avatar" class="hidden" accept="image/*">
                                            
                                            @if($avatar)
                                                <button wire:click="removeAvatar" type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                                    Remove
                                                </button>
                                            @endif
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">JPG, PNG up to 2MB</p>
                                        @error('new_avatar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        
                                        @if($new_avatar)
                                            <button wire:click="updateAvatar" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                                                Save Avatar
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Profile Form -->
                                <form wire:submit.prevent="updateProfile">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                            <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                            <input type="email" wire:model="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-500 @enderror">
                                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                            <input type="text" wire:model="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Account Tab -->
                    @if($activeTab === 'account')
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-6">Account Settings</h3>
                                
                                <div class="space-y-6">
                                    <!-- Current Role -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Current Role</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $user->role->display_name ?? 'N/A' }}</p>
                                    </div>

                                    <!-- Current Organization -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Current Organization</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $user->currentOrganization->name ?? 'N/A' }}</p>
                                    </div>

                                    <!-- Account Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Account Status</label>
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>

                                    <!-- Member Since -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Member Since</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                                    </div>

                                    <!-- Delete Account -->
                                    <div class="pt-6 border-t border-gray-200">
                                        <h4 class="text-sm font-medium text-red-900 mb-2">Danger Zone</h4>
                                        <p class="text-sm text-gray-600 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                                        <button wire:click="deleteAccount" type="button" class="px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                            Delete Account
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Security Tab -->
                    @if($activeTab === 'security')
                        <div class="space-y-6">
                            <!-- Change Password -->
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-6">Change Password</h3>
                                    
                                    <form wire:submit.prevent="updatePassword">
                                        <div class="space-y-4">
                                            <div>
                                                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                                <input type="password" wire:model="current_password" id="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>

                                            <div>
                                                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                                <input type="password" wire:model="new_password" id="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('new_password') border-red-500 @enderror">
                                                @error('new_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                                <input type="password" wire:model="new_password_confirmation" id="new_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end">
                                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                                Update Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Two-Factor Authentication -->
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Two-Factor Authentication</h3>
                                    <p class="text-sm text-gray-600 mb-6">Add an extra layer of security to your account</p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Status</p>
                                            <p class="text-sm text-gray-600">
                                                @if($two_factor_enabled)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Enabled
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Disabled
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            @if($two_factor_enabled)
                                                <button wire:click="disableTwoFactor" type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    Disable
                                                </button>
                                            @else
                                                <button wire:click="enableTwoFactor" type="button" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                                    Enable
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notifications Tab -->
                    @if($activeTab === 'notifications')
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-6">Notification Preferences</h3>
                                
                                <form wire:submit.prevent="updatePreferences">
                                    <div class="space-y-6">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input wire:model="notification_email" id="notification_email" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="notification_email" class="font-medium text-gray-700">Email Notifications</label>
                                                <p class="text-gray-500">Receive notifications via email</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input wire:model="notification_sms" id="notification_sms" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="notification_sms" class="font-medium text-gray-700">SMS Notifications</label>
                                                <p class="text-gray-500">Receive notifications via SMS</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input wire:model="notification_push" id="notification_push" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="notification_push" class="font-medium text-gray-700">Push Notifications</label>
                                                <p class="text-gray-500">Receive push notifications in browser</p>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-200 pt-6">
                                            <h4 class="text-sm font-medium text-gray-900 mb-4">Preferences</h4>
                                            
                                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                <div>
                                                    <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                                                    <select wire:model="language" id="language" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        <option value="en">English</option>
                                                        <option value="hi">हिन्दी (Hindi)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                                                    <select wire:model="timezone" id="timezone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                                                        <option value="UTC">UTC</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for="theme" class="block text-sm font-medium text-gray-700">Theme</label>
                                                    <select wire:model="theme" id="theme" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                        <option value="light">Light</option>
                                                        <option value="dark">Dark</option>
                                                        <option value="auto">Auto</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                            Save Preferences
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Organizations Tab -->
                    @if($activeTab === 'organizations')
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-6">Your Organizations</h3>
                                
                                <div class="space-y-4">
                                    @foreach($organizations as $org)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3">
                                                        <h4 class="text-base font-medium text-gray-900">{{ $org->name }}</h4>
                                                        @if($org->pivot->is_default)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                Default
                                                            </span>
                                                        @endif
                                                        @if($org->pivot->is_active)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                Active
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                                Inactive
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        Role: {{ \App\Models\Role::find($org->pivot->role_id)->display_name ?? 'N/A' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">{{ $org->email }}</p>
                                                </div>
                                                @if($user->current_organization_id !== $org->id && $org->pivot->is_active)
                                                    <button wire:click="$emit('switchOrganization', {{ $org->id }})" class="ml-4 px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                        Switch
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($organizations->isEmpty())
                                        <p class="text-sm text-gray-500 text-center py-6">No organizations assigned</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('profile_success'))
        <div class="fixed bottom-4 right-4 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-lg z-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('profile_success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session()->has('password_success'))
        <div class="fixed bottom-4 right-4 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-lg z-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('password_success') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>