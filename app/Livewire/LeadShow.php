<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadStatus;
use Illuminate\Support\Facades\Auth;

class LeadShow extends Component
{
    public $lead;
    public $activities;

    // Activity form
    public $showActivityForm = false;
    public $activity_type = 'call';
    public $subject;
    public $description;
    public $activity_date;
    public $next_follow_up_date;
    public $previousLeadId;
    public $nextLeadId;

    // Quick status update
    public $new_status_id;

    protected $rules = [
        'activity_type' => 'required|in:call,email,meeting,note,status_change,assignment,other',
        'subject' => 'required|string|max:255',
        'description' => 'nullable|string',
        'activity_date' => 'required|date',
        'next_follow_up_date' => 'nullable|date',
    ];

    public function mount($lead)
    {
        $this->lead = Lead::with(['source', 'status', 'assignedTo', 'customer'])

            ->findOrFail($lead);

        $this->loadActivities();
        $this->loadNavigation();
        $this->activity_date = now()->format('Y-m-d\TH:i');
        $this->new_status_id = $this->lead->lead_status_id;
    }

    public function loadActivities()
    {
        $this->activities = LeadActivity::with('user')
            ->where('lead_id', $this->lead->id)
            ->orderBy('activity_date', 'desc')
            ->get();
    }
    public function loadNavigation()
    {
        $user = Auth::user();

        // Get base query with same filters as list
        $baseQuery = Lead::where('organization_id', $user->currentOrganization->id);

        // Apply same filtering logic as index page
        if (!$user->canManageAllLeads()) {
            $baseQuery = $baseQuery->where('assigned_to', $user->id);
        }

        // Get previous lead (ID less than current, ordered desc)
        $this->previousLeadId = (clone $baseQuery)
            ->where('id', '<', $this->lead->id)
            ->orderBy('id', 'desc')
            ->value('id');

        // Get next lead (ID greater than current, ordered asc)
        $this->nextLeadId = (clone $baseQuery)
            ->where('id', '>', $this->lead->id)
            ->orderBy('id', 'asc')
            ->value('id');
    }
    public function toggleActivityForm()
    {
        $this->showActivityForm = !$this->showActivityForm;

        if ($this->showActivityForm) {
            $this->reset(['activity_type', 'subject', 'description', 'next_follow_up_date']);
            $this->activity_date = now()->format('Y-m-d\TH:i');
            $this->activity_type = 'call';
        }
    }

    public function saveActivity()
    {
        $this->validate();

        LeadActivity::create([
            'lead_id' => $this->lead->id,
            'user_id' => Auth::id(),
            'activity_type' => $this->activity_type,
            'subject' => $this->subject,
            'description' => $this->description,
            'activity_date' => $this->activity_date,
            'next_follow_up_date' => $this->next_follow_up_date,
        ]);

        // Update lead's next follow-up date if provided
        if ($this->next_follow_up_date) {
            $this->lead->update([
                'next_follow_up_date' => $this->next_follow_up_date,
            ]);
        }

        session()->flash('success', 'Activity added successfully!');

        $this->loadActivities();
        $this->toggleActivityForm();
        $this->lead->refresh();
    }

    public function quickAddCall()
    {
        LeadActivity::create([
            'lead_id' => $this->lead->id,
            'user_id' => Auth::id(),
            'activity_type' => 'call',
            'subject' => 'Quick Call',
            'description' => 'Made a call to the lead',
            'activity_date' => now(),
        ]);

        session()->flash('success', 'Call activity added!');
        $this->loadActivities();
    }

    public function quickAddEmail()
    {
        LeadActivity::create([
            'lead_id' => $this->lead->id,
            'user_id' => Auth::id(),
            'activity_type' => 'email',
            'subject' => 'Email Sent',
            'description' => 'Sent email to the lead',
            'activity_date' => now(),
        ]);

        session()->flash('success', 'Email activity added!');
        $this->loadActivities();
    }
    public function quickAddWhastapp()
    {
        LeadActivity::create([
            'lead_id' => $this->lead->id,
            'user_id' => Auth::id(),
            'activity_type' => 'email',
            'subject' => 'Whatsapp message Sent',
            'description' => 'Whatsapp message Sent',
            'activity_date' => now(),
        ]);

        session()->flash('success', 'Whsapp activity added!');
        $this->loadActivities();
    }

    public function updateStatus()
    {
        if ($this->new_status_id != $this->lead->lead_status_id) {
            $oldStatus = $this->lead->status;
            $newStatus = LeadStatus::find($this->new_status_id);

            $this->lead->update([
                'lead_status_id' => $this->new_status_id,
            ]);

            // Log status change activity
            LeadActivity::create([
                'lead_id' => $this->lead->id,
                'user_id' => Auth::id(),
                'activity_type' => 'status_change',
                'subject' => 'Status Changed',
                'description' => "Status changed from {$oldStatus->display_name} to {$newStatus->display_name}",
                'activity_date' => now(),
            ]);

            session()->flash('success', 'Status updated successfully!');
            $this->loadActivities();
            $this->lead->refresh();
        }
    }
    public function quickAddNote()
    {
        if (!$this->quick_note)
            return;

        $text = strtolower($this->quick_note);

        // 🔍 Detect type automatically
        if (str_contains($text, 'call')) {
            $type = 'call';
            $subject = 'Call Log';
        } elseif (str_contains($text, 'whatsapp') || str_contains($text, 'msg') || str_contains($text, 'message')) {
            $type = 'other';
            $subject = 'Message Sent';
        } elseif (str_contains($text, 'email')) {
            $type = 'email';
            $subject = 'Email Log';
        } else {
            $type = 'note';
            $subject = 'Quick Note';
        }

        LeadActivity::create([
            'lead_id' => $this->lead->id,
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'subject' => $subject,
            'description' => $this->quick_note,
            'activity_date' => now(),
        ]);

        $this->quick_note = '';

        session()->flash('success', 'Activity logged!');
        $this->loadActivities();
    }

    public function convertToCustomer()
    {


        try {
            $customer = $this->lead->convertToCustomer();
            session()->flash('success', 'Lead converted to customer successfully!');
            $this->lead->refresh();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to convert lead: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $statuses = LeadStatus::active()->get();

        return view('livewire.lead-show', [
            'statuses' => $statuses,
        ])->layout('layouts.app');
    }
}