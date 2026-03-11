<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\RepurchaseReminder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FollowupsDashboard extends Component
{
    public $selectedDate;
    public $filterAssignedTo = '';
    public $filterType = 'all'; // all, leads, repurchase
    public $showCompleted = false;

    // Quick action
    public $quickActionLead;
    public $quick_note;

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function updatedSelectedDate()
    {
        // Refresh data when date changes
    }

    public function quickComplete($leadId)
    {
        $this->quickActionLead = $leadId;
    }

    public function saveQuickAction()
    {
        $this->validate([
            'quick_note' => 'nullable|string|max:500',
        ]);

        $lead = Lead::find($this->quickActionLead);
        
        if ($lead) {
            // Mark as completed
            $lead->activities()->create([
                'user_id' => Auth::id(),
                'activity_type' => 'call',
                'subject' => 'Follow-up Completed',
                'description' => $this->quick_note ?? 'Follow-up completed',
                'activity_date' => now(),
            ]);

            // Clear next follow-up date
            $lead->update([
                'next_follow_up_date' => null,
            ]);

            session()->flash('success', 'Follow-up marked as completed!');
            $this->quickActionLead = null;
            $this->quick_note = '';
        }
    }

    public function snooze($leadId, $days)
    {
        $lead = Lead::find($leadId);
        
        if ($lead) {
            $newDate = now()->addDays($days);
            
            $lead->update([
                'next_follow_up_date' => $newDate,
            ]);

            $lead->activities()->create([
                'user_id' => Auth::id(),
                'activity_type' => 'note',
                'subject' => 'Follow-up Snoozed',
                'description' => "Follow-up snoozed for {$days} days to {$newDate->format('M d, Y')}",
                'activity_date' => now(),
            ]);

            session()->flash('success', "Follow-up snoozed for {$days} days!");
        }
    }

    public function getLeadFollowupsProperty()
    {
        $user = Auth::user();
        $date = Carbon::parse($this->selectedDate);
        
        $query = Lead::with(['source', 'status', 'assignedTo', 'customer'])
            ->forOrganization($user->current_organization_id)
            ->whereDate('next_follow_up_date', $date);

        if ($this->filterAssignedTo) {
            $query->where('assigned_to', $this->filterAssignedTo);
        } elseif (!$user->canManageAllLeads()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('next_follow_up_date')->get();
    }

    public function getRepurchaseFollowupsProperty()
    {
        $user = Auth::user();
        $date = Carbon::parse($this->selectedDate);
        
        $query = RepurchaseReminder::with(['customer', 'product', 'assignedTo'])
            ->whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })
            ->whereDate('next_reminder_date', $date)
            ->where('status', 'pending');

        if ($this->filterAssignedTo) {
            $query->where('assigned_to', $this->filterAssignedTo);
        } elseif (!$user->canManageAllLeads()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('next_reminder_date')->get();
    }

    public function getOverdueLeadsProperty()
    {
        $user = Auth::user();
        
        $query = Lead::with(['source', 'status', 'assignedTo', 'customer'])
            ->forOrganization($user->current_organization_id)
            ->where('next_follow_up_date', '<', now()->startOfDay());

        if ($this->filterAssignedTo) {
            $query->where('assigned_to', $this->filterAssignedTo);
        } elseif (!$user->canManageAllLeads()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('next_follow_up_date')->limit(10)->get();
    }

    public function getUpcomingLeadsProperty()
    {
        $user = Auth::user();
        
        $query = Lead::with(['source', 'status', 'assignedTo', 'customer'])
            ->forOrganization($user->current_organization_id)
            ->where('next_follow_up_date', '>', now()->endOfDay())
            ->where('next_follow_up_date', '<=', now()->addDays(7));

        if ($this->filterAssignedTo) {
            $query->where('assigned_to', $this->filterAssignedTo);
        } elseif (!$user->canManageAllLeads()) {
            $query->where('assigned_to', $user->id);
        }

        return $query->orderBy('next_follow_up_date')->limit(10)->get();
    }

    public function getStatsProperty()
    {
        $user = Auth::user();
        
        $baseQuery = Lead::forOrganization($user->current_organization_id);
        
        if (!$user->canManageAllLeads()) {
            $baseQuery = $baseQuery->where('assigned_to', $user->id);
        }

        return [
            'today' => (clone $baseQuery)->followUpToday()->count(),
            'overdue' => (clone $baseQuery)->overdue()->count(),
            'this_week' => (clone $baseQuery)
                ->whereBetween('next_follow_up_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'next_week' => (clone $baseQuery)
                ->whereBetween('next_follow_up_date', [
                    now()->addWeek()->startOfWeek(),
                    now()->addWeek()->endOfWeek()
                ])
                ->count(),
        ];
    }

    public function render()
    {
        $agents = User::whereHas('organizations', function ($q) {
            $q->where('organizations.id', Auth::user()->current_organization_id);
        })->get();

        return view('livewire.followups-dashboard', [
            'leadFollowups' => $this->leadFollowups,
            'repurchaseFollowups' => $this->repurchaseFollowups,
            'overdueLeads' => $this->overdueLeads,
            'upcomingLeads' => $this->upcomingLeads,
            'stats' => $this->stats,
            'agents' => $agents,
        ])->layout('layouts.app');
    }
}