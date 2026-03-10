<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OrganizationSwitcher extends Component
{
    public $organizations;
    public $currentOrganization;
    public $showDropdown = false;

    public function mount()
    {
        $this->loadOrganizations();
    }

    public function loadOrganizations()
    {
        $user = Auth::user();
        $this->organizations = $user->organizations()
            ->wherePivot('is_active', true)
            ->withPivot('role_id')
            ->get();
        $this->currentOrganization = $user->currentOrganization;
    }

    public function switchOrganization($organizationId)
    {
        try {
            $user = Auth::user();
            
            // Verify user has access to this organization
            if (!$user->hasAccessToOrganization($organizationId)) {
                session()->flash('error', 'You do not have access to this organization.');
                return;
            }

            // Switch organization
            $user->switchOrganization($organizationId);
            
            session()->flash('success', 'Switched to ' . $user->currentOrganization->name);
            
            // Refresh the page to reload all data with new organization context
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to switch organization: ' . $e->getMessage());
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function render()
    {
        return view('livewire.organization-switcher');
    }
}