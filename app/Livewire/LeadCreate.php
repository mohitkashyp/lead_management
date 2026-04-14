<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use App\Services\PincodeService;
use Illuminate\Support\Facades\Auth;

class LeadCreate extends Component
{
    public $name;
    public $email;
    public $phone;
    public $alternate_phone;
    public $lead_source_id;
    public $lead_status_id;
    public $assigned_to;
    public $address;
    public $city;
    public $state;
    public $pincode;
    public $product_interest;
    public $estimated_value;
    public $next_follow_up_date;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'required|string|min:10|max:20',
        'alternate_phone' => 'nullable|string|max:20|min:10',
        'lead_source_id' => 'required|exists:lead_sources,id',
        'lead_status_id' => 'required|exists:lead_statuses,id',
        'assigned_to' => 'nullable|exists:users,id',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'pincode' => 'nullable|string|max:10',
        'product_interest' => 'nullable|string|max:255',
        'estimated_value' => 'nullable|numeric|min:0',
        'next_follow_up_date' => 'nullable|date',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        // Set default status to 'new'
        $newStatus = LeadStatus::where('name', 'new')->first();
        $this->lead_status_id = $newStatus?->id;

        // Auto-assign to current user if agent
        if (!Auth::user()->isAdmin()) {
            $this->assigned_to = Auth::id();
        }
    }
    public function updatedPincode($value)
    {
        $pincodeService = new PincodeService();

        $location = $pincodeService->getLocationByPincode($value);

        $this->city = $location['city'];
        $this->state = $location['state'];


    }

    public function save()
    {
        $this->validate();
        $existingLead = Lead::where('phone', $this->phone)->first();

        if ($existingLead) {
            session()->flash('info', 'Lead already exists. Redirected to existing lead.');

            return redirect()->route('leads.show', $existingLead->id);
        }
        $lead = Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'alternate_phone' => $this->alternate_phone,
            'lead_source_id' => $this->lead_source_id,
            'lead_status_id' => $this->lead_status_id,
            'assigned_to' => $this->assigned_to,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'product_interest' => $this->product_interest,
            'estimated_value' => $this->estimated_value,
            'next_follow_up_date' => $this->next_follow_up_date,
            'notes' => $this->notes,
            'organization_id' => Auth::user()->currentOrganization->id,
        ]);

        // Create initial activity
        $lead->activities()->create([
            'user_id' => Auth::id(),
            'activity_type' => 'note',
            'subject' => 'Lead Created',
            'description' => 'Lead was created in the system.',
            'activity_date' => now(),
        ]);

        session()->flash('success', 'Lead created successfully!');

        return redirect()->route('leads.show', $lead->id);
    }

    public function render()
    {
        $sources = LeadSource::active()->get();
        $statuses = LeadStatus::active()->get();
        $agents = User::where('role_id', '!=', null)->get();

        return view('livewire.lead-create', [
            'sources' => $sources,
            'statuses' => $statuses,
            'agents' => $agents,
        ])->layout('layouts.app');
    }
}