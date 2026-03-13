<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class OrganizationsList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        // Get organizations where user is admin or show all if super admin
        $organizations = Organization::query()
            ->when(!Auth::user()->isAdmin(), function($query) use ($user) {
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('users.id', $user->id)
                      ->whereHas('roles', function($r) {
                          $r->where('name', 'admin');
                      });
                });
            })
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus !== '', function($query) {
                $query->where('is_active', $this->filterStatus);
            })
            ->withCount('users')
            ->latest()
            ->paginate(10);

        return view('livewire.organizations-list', [
            'organizations' => $organizations,
        ])->layout('layouts.app');
    }
}