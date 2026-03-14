<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeadsList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSource = '';
    public $filterStatus = '';
    public $filterAssignedTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $currentOrganization = 'desc';
    public $activityType = 'call';
    public $activityNote = '';
    public $followupDate = '';
    public $selectedLead = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterSource' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterAssignedTo' => ['except' => ''],
    ];
    public function mount()
    {
        $this->currentOrganization = Auth::user()->currentOrganization;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function openFollowup($leadId)
    {
        $this->selectedLead = $leadId;
        $this->activityType = 'call';
    }
    public function saveFollowup()
    {
        $this->validate([
            'activityNote' => 'required',
            'followupDate' => 'nullable|date'
        ]);

        LeadActivity::create([
            'lead_id' => $this->selectedLead,
            'user_id' => Auth::id(),
            'activity_type' => $this->activityType,
            'subject' => ucfirst($this->activityType),
            'description' => $this->activityNote,
            'activity_date' => now(),
            'next_follow_up_date' => $this->followupDate
        ]);
        Lead::where('id', $this->selectedLead)->update(['next_follow_up_date' => $this->followupDate]);

        $this->reset([
            'activityNote',
            'followupDate',
            'selectedLead'
        ]);

        session()->flash('success', 'Follow-up saved.');
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterSource', 'filterStatus', 'filterAssignedTo']);
        $this->resetPage();
    }

    public function deleteLead($leadId)
    {
        $lead = Lead::find($leadId);

        if ($lead) {
            $lead->delete();
            session()->flash('success', 'Lead deleted successfully.');
        }
    }

    public function render()
    {
        $user = Auth::user();
       
        $leads = Lead::with(['source', 'status', 'assignedTo', 'customer'])
            ->where('organization_id', $user->currentOrganization->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('lead_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterSource, function ($query) {
                $query->where('lead_source_id', $this->filterSource);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('lead_status_id', $this->filterStatus);
            })
            ->when($this->filterAssignedTo, function ($query) {
                $query->where('assigned_to', $this->filterAssignedTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $sources = LeadSource::active()->get();
        $statuses = LeadStatus::active()->get();
        $agents = User::where('role_id', '!=', null)->get();

        return view('livewire.leads-list', [
            'leads' => $leads,
            'sources' => $sources,
            'statuses' => $statuses,
            'agents' => $agents,
        ])->layout('layouts.app');
    }
}