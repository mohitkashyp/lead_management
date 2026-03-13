<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Organization;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationCreate extends Component
{
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
    
    // Creator becomes admin
    public $make_me_admin = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:organizations,slug',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'pincode' => 'nullable|string|max:10',
        'country' => 'required|string|max:100',
        'gstin' => 'nullable|string|max:15',
    ];

    public function updatedName($value)
    {
        // Auto-generate slug from name
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Create organization
            $organization = Organization::create([
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

            // Get admin role
            $adminRole = Role::where('name', 'admin')->first();
            
            if (!$adminRole) {
                throw new \Exception('Admin role not found. Please seed roles first.');
            }

            // Add creator as admin of this organization
            $user = Auth::user();
            
            $organization->users()->attach($user->id, [
                'role_id' => $adminRole->id,
                'is_default' => false,
                'is_active' => true,
            ]);

            // If this is user's first organization or they want it as default
            if (!$user->current_organization_id) {
                $user->update([
                    'current_organization_id' => $organization->id,
                ]);
            }

            DB::commit();

            session()->flash('success', 'Organization created successfully! You are now an admin of this organization.');
            
            return redirect()->route('organizations.edit', $organization->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create organization: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.organization-create')->layout('layouts.app');
    }
}