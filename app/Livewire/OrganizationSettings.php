<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingProvider;
use App\Services\SecretManager;

class OrganizationSettings extends Component
{
    public $organization;

    // organization fields
    public $name;
    public $email;
    public $phone;
    public $website;
    public $gstin;

    public $address;
    public $city;
    public $state;
    public $pincode;
    public $country;

    // shipping
    public $providers;
    public $shipping_provider_id;

    // secrets
    public $api_key;

    // pickup details
    public $pickup_name;
    public $pickup_phone;
    public $pickup_address;
    public $pickup_city;
    public $pickup_state;
    public $pickup_pincode;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        $this->organization = Auth::user()->currentOrganization;

        $this->providers = ShippingProvider::where('is_active', true)->get();

        // org fields
        $this->name = $this->organization->name;
        $this->email = $this->organization->email;
        $this->phone = $this->organization->phone;
        $this->website = $this->organization->website;
        $this->gstin = $this->organization->gstin;

        $this->address = $this->organization->address;
        $this->city = $this->organization->city;
        $this->state = $this->organization->state;
        $this->pincode = $this->organization->pincode;
        $this->country = $this->organization->country;

        $this->shipping_provider_id = $this->organization->shipping_provider_id;

        // load secrets
        $this->loadSecrets();
    }

    public function loadSecrets()
    {
        if (!$this->shipping_provider_id) {
            return;
        }

        $provider = ShippingProvider::find($this->shipping_provider_id);

        if (!$provider) {
            return;
        }

        $orgId = $this->organization->id;

        $this->api_key = SecretManager::get($orgId, $provider->id, 'api_key');

        $this->pickup_name = SecretManager::get($orgId, $provider->id, 'pickup_name');
        $this->pickup_phone = SecretManager::get($orgId, $provider->id, 'pickup_phone');
        $this->pickup_address = SecretManager::get($orgId, $provider->id, 'pickup_address');
        $this->pickup_city = SecretManager::get($orgId, $provider->id, 'pickup_city');
        $this->pickup_state = SecretManager::get($orgId, $provider->id, 'pickup_state');
        $this->pickup_pincode = SecretManager::get($orgId, $provider->id, 'pickup_pincode');
    }

    public function updatedShippingProviderId()
    {
        $this->loadSecrets();
    }

    public function save()
    {
        $this->validate();

        $this->organization->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'gstin' => $this->gstin,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'country' => $this->country,
            'shipping_provider_id' => $this->shipping_provider_id
        ]);

        if ($this->shipping_provider_id) {

            $orgId = $this->organization->id;
            $providerId = $this->shipping_provider_id;

            SecretManager::set($orgId, $providerId, 'api_key', $this->api_key);

            SecretManager::set($orgId, $providerId, 'pickup_name', $this->pickup_name);
            SecretManager::set($orgId, $providerId, 'pickup_phone', $this->pickup_phone);
            SecretManager::set($orgId, $providerId, 'pickup_address', $this->pickup_address);
            SecretManager::set($orgId, $providerId, 'pickup_city', $this->pickup_city);
            SecretManager::set($orgId, $providerId, 'pickup_state', $this->pickup_state);
            SecretManager::set($orgId, $providerId, 'pickup_pincode', $this->pickup_pincode);
        }

        session()->flash('success', 'Organization settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.organization-settings')
            ->layout('layouts.app');
    }
}