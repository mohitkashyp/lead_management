<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrganizationUsers extends Component
{
    use WithPagination;

    public $organization;
    public $users;
    public $roles;
    
    // Add user form
    public $showAddUserForm = false;
    public $user_name;
    public $user_email;
    public $user_password;
    public $user_role_id;
    public $is_existing_user = false;
    public $existing_user_id;
    
    // Edit user role
    public $editingUser;
    public $editing_role_id;

    protected $rules = [
        'user_name' => 'required|string|max:255',
        'user_email' => 'required|email|unique:users,email',
        'user_password' => 'required|string|min:8',
        'user_role_id' => 'required|exists:roles,id',
    ];

    public function mount()
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $this->organization = Auth::user()->currentOrganization;
        $this->roles = Role::all();
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = $this->organization->users()
            ->withPivot(['role_id', 'is_default', 'is_active'])
            ->with('role')
            ->get();
    }

    public function toggleAddUserForm()
    {
        $this->showAddUserForm = !$this->showAddUserForm;
        
        if ($this->showAddUserForm) {
            $this->reset(['user_name', 'user_email', 'user_password', 'user_role_id', 'is_existing_user', 'existing_user_id']);
        }
    }

    public function addNewUser()
    {
        $this->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'user_password' => 'required|string|min:8',
            'user_role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Create new user
            $user = User::create([
                'name' => $this->user_name,
                'email' => $this->user_email,
                'password' => Hash::make($this->user_password),
                'role_id' => $this->user_role_id,
                'current_organization_id' => $this->organization->id,
            ]);

            // Add to organization
            $this->organization->addUser($user, $this->user_role_id, true);

            session()->flash('success', 'User added successfully!');
            
            $this->loadUsers();
            $this->toggleAddUserForm();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add user: ' . $e->getMessage());
        }
    }

    public function addExistingUser()
    {
        $this->validate([
            'existing_user_id' => 'required|exists:users,id',
            'user_role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user = User::find($this->existing_user_id);
            
            // Check if user already in organization
            if ($this->organization->users()->where('user_id', $user->id)->exists()) {
                session()->flash('error', 'User is already in this organization.');
                return;
            }

            // Add to organization
            $this->organization->addUser($user, $this->user_role_id, false);

            session()->flash('success', 'User added to organization successfully!');
            
            $this->loadUsers();
            $this->toggleAddUserForm();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add user: ' . $e->getMessage());
        }
    }

    public function startEditingRole($userId)
    {
        $user = $this->users->where('id', $userId)->first();
        $this->editingUser = $userId;
        $this->editing_role_id = $user->pivot->role_id;
    }

    public function updateUserRole($userId)
    {
        try {
            $user = User::find($userId);
            $this->organization->updateUserRole($user, $this->editing_role_id);

            session()->flash('success', 'User role updated successfully!');
            
            $this->loadUsers();
            $this->editingUser = null;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function removeUser($userId)
    {
        try {
            $user = User::find($userId);
            
            // Don't allow removing yourself
            if ($user->id === Auth::id()) {
                session()->flash('error', 'You cannot remove yourself from the organization.');
                return;
            }

            $this->organization->removeUser($user);

            session()->flash('success', 'User removed from organization successfully!');
            
            $this->loadUsers();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }

    public function toggleUserStatus($userId)
    {
        try {
            $userOrg = \DB::table('user_organizations')
                ->where('user_id', $userId)
                ->where('organization_id', $this->organization->id)
                ->first();

            \DB::table('user_organizations')
                ->where('user_id', $userId)
                ->where('organization_id', $this->organization->id)
                ->update(['is_active' => !$userOrg->is_active]);

            session()->flash('success', 'User status updated successfully!');
            
            $this->loadUsers();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $availableUsers = User::whereDoesntHave('organizations', function ($query) {
            $query->where('organizations.id', $this->organization->id);
        })->get();

        return view('livewire.organization-users', [
            'availableUsers' => $availableUsers,
        ])->layout('layouts.app');
    }
}