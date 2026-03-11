<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserEdit extends Component
{
    public $user;
    public $isCreating = false;
    
    // User fields
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role_id;
    public $is_active = true;
    
    // Organization assignments
    public $user_organizations = [];
    public $available_organizations = [];

    public function mount($user = null)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($user) {
            $this->user = User::with('organizations')->findOrFail($user);
            $this->fillFromUser();
        } else {
            $this->isCreating = true;
        }

        $this->loadAvailableOrganizations();
    }

    public function fillFromUser()
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->role_id = $this->user->role_id;
        $this->is_active = $this->user->is_active ?? true;
        
        // Load user's organization assignments
        foreach ($this->user->organizations as $org) {
            $this->user_organizations[] = [
                'organization_id' => $org->id,
                'organization_name' => $org->name,
                'role_id' => $org->pivot->role_id,
                'is_default' => $org->pivot->is_default,
                'is_active' => $org->pivot->is_active,
            ];
        }
    }

    public function loadAvailableOrganizations()
    {
        // Get organizations that current admin has access to
        $this->available_organizations = Auth::user()->organizations;
    }

    public function addOrganization()
    {
        $this->user_organizations[] = [
            'organization_id' => '',
            'role_id' => '',
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function removeOrganization($index)
    {
        unset($this->user_organizations[$index]);
        $this->user_organizations = array_values($this->user_organizations);
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id ?? null)],
            'role_id' => 'required|exists:roles,id',
        ];

        if ($this->isCreating || $this->password) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules);

        try {
            if ($this->isCreating) {
                // Create new user
                $this->user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role_id' => $this->role_id,
                ]);
            } else {
                // Update existing user
                $updateData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'role_id' => $this->role_id,
                ];

                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }

                $this->user->update($updateData);
            }

            // Sync organization assignments
            foreach ($this->user_organizations as $orgAssignment) {
                if (empty($orgAssignment['organization_id']) || empty($orgAssignment['role_id'])) {
                    continue;
                }

                $organization = Organization::find($orgAssignment['organization_id']);
                
                // Check if already assigned
                $exists = $this->user->organizations()->where('organizations.id', $organization->id)->exists();
                
                if ($exists) {
                    // Update existing assignment
                    $this->user->organizations()->updateExistingPivot($organization->id, [
                        'role_id' => $orgAssignment['role_id'],
                        'is_default' => $orgAssignment['is_default'],
                        'is_active' => $orgAssignment['is_active'],
                    ]);
                } else {
                    // Add new assignment
                    $this->user->organizations()->attach($organization->id, [
                        'role_id' => $orgAssignment['role_id'],
                        'is_default' => $orgAssignment['is_default'],
                        'is_active' => $orgAssignment['is_active'],
                    ]);
                }
            }

            // Set default organization if specified
            $defaultOrg = collect($this->user_organizations)->firstWhere('is_default', true);
            if ($defaultOrg) {
                $this->user->update([
                    'current_organization_id' => $defaultOrg['organization_id'],
                ]);
            }

            session()->flash('success', $this->isCreating ? 'User created successfully!' : 'User updated successfully!');
            
            return redirect()->route('organization.users');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $roles = Role::all();
        
        return view('livewire.user-edit', [
            'roles' => $roles,
        ])->layout('layouts.app');;
    }
}