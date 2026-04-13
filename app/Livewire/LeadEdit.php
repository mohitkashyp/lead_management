<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;

class LeadEdit extends Component
{
    public $leadId;

    public $name;
    public $phone;
    public $email;
    public $lead_source_id;
    public $status_id;
    public $assigned_to;
    public $next_follow_up_date;
    public $notes;
    public $address;
    public $city;
    public $state;
    public $pincode;

    public $sources = [];
    public $statuses = [];
    public $agents = [];

    public function mount($lead)
    {
        $lead = Lead::findOrFail($lead);

        $this->leadId = $lead->id;
        $this->name = $lead->name;
        $this->phone = $lead->phone;
        $this->email = $lead->email;
        $this->lead_source_id = $lead->lead_source_id;
        $this->status_id = $lead->status_id;
        $this->assigned_to = $lead->assigned_to;
        $this->next_follow_up_date = $lead->next_follow_up_date;
        $this->notes = $lead->notes;
        $this->address = $lead->address;
        $this->city = $lead->city;
        $this->state = $lead->state;
        $this->pincode = $lead->pincode;

        $this->sources = LeadSource::all();
        $this->statuses = LeadStatus::all();
        $this->agents = User::all();
    }

    public function updateLead()
    {
        $this->validate([
            'name' => 'required',
            'phone' => 'required',
            'pincode' => 'nullable|digits:6',
        ]);

        $lead = Lead::findOrFail($this->leadId);

        $lead->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'lead_source_id' => $this->lead_source_id,
            'status_id' => $this->status_id,
            'assigned_to' => $this->assigned_to,
            'next_follow_up_date' => $this->next_follow_up_date,
            'notes' => $this->notes,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
        ]);

        session()->flash('success', 'Lead updated successfully.');

        return redirect()->route('leads.show', $this->leadId);
    }

    public function render()
    {
        $lead = Lead::findOrFail($this->leadId);
        return view('livewire.lead-edit', [
            'lead' => $lead,
            'sources' => $this->sources,
            'statuses' => $this->statuses,
            'agents' => $this->agents,
        ])->layout('layouts.app');
    }
}