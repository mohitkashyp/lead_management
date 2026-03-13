<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Organization;
use App\Models\ShippingProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationEdit extends Component
{
    public $organization;
    
    // Organization fields
    public $name;
    public $slug;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $pincode;
    public $country = 'India';
    public $gstin;
    public $is_active = true;
    
    // Shipping provider configuration
    public $shipping_providers = [];
    public $selected_providers = [];
    public $provider_configs = [];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'pincode' => 'nullable|string|max:10',
        'gstin' => 'nullable|string|max:15',
    ];

    public function mount($organization = null)
    {
        if ($organization) {
            $this->organization = Organization::findOrFail($organization);
            
            // Check if user has permission to edit
            if (!Auth::user()->isAdmin() && !$this->organization->users()->where('users.id', Auth::id())->exists()) {
                abort(403, 'Unauthorized');
            }
            
            $this->fillFromOrganization();
        } else {
            // Creating new organization - user will become admin
            $this->organization = new Organization();
            $this->country = 'India';
            $this->is_active = true;
        }

        $this->loadShippingProviders();
        if ($this->organization->exists) {
            $this->loadOrganizationShippingConfig();
        }
    }

    public function fillFromOrganization()
    {
        $this->name = $this->organization->name;
        $this->slug = $this->organization->slug;
        $this->email = $this->organization->email;
        $this->phone = $this->organization->phone;
        $this->address = $this->organization->address;
        $this->city = $this->organization->city;
        $this->state = $this->organization->state;
        $this->pincode = $this->organization->pincode;
        $this->country = $this->organization->country ?? 'India';
        $this->gstin = $this->organization->gstin;
        $this->is_active = $this->organization->is_active;
    }

    public function loadShippingProviders()
    {
        $this->shipping_providers = ShippingProvider::where('is_active', true)->get();
    }

    public function loadOrganizationShippingConfig()
    {
        $settings = $this->organization->settings ?? [];
        
        // Load selected providers
        $this->selected_providers = $settings['shipping_providers'] ?? [];
        
        // Load provider configurations
        foreach ($this->shipping_providers as $provider) {
            $providerKey = strtolower(str_replace(' ', '_', $provider->name));
            
            $this->provider_configs[$provider->id] = [
                'enabled' => in_array($provider->id, $this->selected_providers),
                'api_key' => $settings['shipping_config'][$providerKey]['api_key'] ?? '',
                'api_secret' => $settings['shipping_config'][$providerKey]['api_secret'] ?? '',
                'api_endpoint' => $settings['shipping_config'][$providerKey]['api_endpoint'] ?? $provider->api_endpoint,
                'pickup_location' => $settings['shipping_config'][$providerKey]['pickup_location'] ?? '',
                'pickup_name' => $settings['shipping_config'][$providerKey]['pickup_name'] ?? $this->organization->name,
                'pickup_phone' => $settings['shipping_config'][$providerKey]['pickup_phone'] ?? $this->organization->phone,
                'pickup_address' => $settings['shipping_config'][$providerKey]['pickup_address'] ?? $this->organization->address,
                'pickup_city' => $settings['shipping_config'][$providerKey]['pickup_city'] ?? $this->organization->city,
                'pickup_state' => $settings['shipping_config'][$providerKey]['pickup_state'] ?? $this->organization->state,
                'pickup_pincode' => $settings['shipping_config'][$providerKey]['pickup_pincode'] ?? $this->organization->pincode,
                'default_courier_id' => $settings['shipping_config'][$providerKey]['default_courier_id'] ?? '',
                'channel_id' => $settings['shipping_config'][$providerKey]['channel_id'] ?? '',
            ];
        }
    }

    public function toggleProvider($providerId)
    {
        $this->provider_configs[$providerId]['enabled'] = !($this->provider_configs[$providerId]['enabled'] ?? false);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $isCreating = !$this->organization->exists;

            // Update or create basic organization info
            $this->organization->fill([
                'name' => $this->name,
                'slug' => $this->slug ?: Str::slug($this->name),
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'pincode' => $this->pincode,
                'country' => $this->country,
                'gstin' => $this->gstin,
                'is_active' => $this->is_active,
            ]);
            
            $this->organization->save();

            // If creating, add user as admin
            if ($isCreating) {
                $adminRole = \App\Models\Role::where('name', 'admin')->first();
                
                $this->organization->users()->attach(Auth::id(), [
                    'role_id' => $adminRole->id,
                    'is_default' => false,
                    'is_active' => true,
                ]);

                // Set as current organization if user has none
                $user = Auth::user();
                if (!$user->current_organization_id) {
                    $user->update([
                        'current_organization_id' => $this->organization->id,
                    ]);
                }
            }

            // Build shipping configuration
            $shippingConfig = [];
            $enabledProviders = [];

            foreach ($this->provider_configs as $providerId => $config) {
                if ($config['enabled']) {
                    $provider = ShippingProvider::find($providerId);
                    $providerKey = strtolower(str_replace(' ', '_', $provider->name));
                    
                    $enabledProviders[] = $providerId;
                    
                    $shippingConfig[$providerKey] = [
                        'api_key' => $config['api_key'] ?? '',
                        'api_secret' => $config['api_secret'] ?? '',
                        'api_endpoint' => $config['api_endpoint'] ?? '',
                        'pickup_location' => $config['pickup_location'] ?? '',
                        'pickup_name' => $config['pickup_name'] ?? '',
                        'pickup_phone' => $config['pickup_phone'] ?? '',
                        'pickup_address' => $config['pickup_address'] ?? '',
                        'pickup_city' => $config['pickup_city'] ?? '',
                        'pickup_state' => $config['pickup_state'] ?? '',
                        'pickup_pincode' => $config['pickup_pincode'] ?? '',
                        'default_courier_id' => $config['default_courier_id'] ?? '',
                        'channel_id' => $config['channel_id'] ?? '',
                    ];
                }
            }

            // Update organization settings
            $currentSettings = $this->organization->settings ?? [];
            $currentSettings['shipping_providers'] = $enabledProviders;
            $currentSettings['shipping_config'] = $shippingConfig;

            $this->organization->update([
                'settings' => $currentSettings,
            ]);

            DB::commit();

            session()->flash('success', $isCreating ? 'Organization created successfully!' : 'Organization updated successfully!');
            
            return redirect()->route('organizations.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to save organization: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.organization-edit')->layout('layouts.app');
    }
}