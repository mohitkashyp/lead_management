<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RepurchaseReminder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RepurchaseManagement extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterAssignedTo = '';
    public $filterProduct = '';
    public $search = '';
    public $sortField = 'next_reminder_date';
    public $sortDirection = 'asc';

    // Mark as contacted
    public $contactingReminder;
    public $contact_notes;
    public $contacted_at;
    public $next_contact_date;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterAssignedTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function startContact($reminderId)
    {
        $this->contactingReminder = $reminderId;
        $this->contacted_at = now()->format('Y-m-d');
        $this->next_contact_date = now()->addDays(7)->format('Y-m-d');
        $this->contact_notes = '';
    }

    public function markAsContacted()
    {
        $this->validate([
            'contacted_at' => 'required|date',
            'contact_notes' => 'nullable|string',
        ]);

        $reminder = RepurchaseReminder::find($this->contactingReminder);
        
        if ($reminder) {
            $reminder->update([
                'status' => 'contacted',
                'last_contacted_at' => $this->contacted_at,
                'notes' => ($reminder->notes ?? '') . "\n[" . now()->format('Y-m-d H:i') . "] " . $this->contact_notes,
            ]);

            if ($this->next_contact_date) {
                $reminder->update([
                    'next_reminder_date' => $this->next_contact_date,
                ]);
            }

            // Log activity on customer's leads
            $customer = $reminder->customer;
            if ($customer->leads()->exists()) {
                $customer->leads()->first()->activities()->create([
                    'user_id' => Auth::id(),
                    'activity_type' => 'call',
                    'subject' => 'Repurchase Follow-up',
                    'description' => 'Contacted for product repurchase: ' . $reminder->product->name . "\n" . $this->contact_notes,
                    'activity_date' => $this->contacted_at,
                ]);
            }

            session()->flash('success', 'Marked as contacted successfully!');
            $this->contactingReminder = null;
        }
    }

    public function markAsCompleted($reminderId)
    {
        $reminder = RepurchaseReminder::find($reminderId);
        
        if ($reminder) {
            $reminder->update([
                'status' => 'completed',
            ]);

            session()->flash('success', 'Marked as completed!');
        }
    }

    public function markAsNotInterested($reminderId)
    {
        $reminder = RepurchaseReminder::find($reminderId);
        
        if ($reminder) {
            $reminder->update([
                'status' => 'not_interested',
            ]);

            session()->flash('success', 'Marked as not interested!');
        }
    }

    public function resetReminder($reminderId)
    {
        $reminder = RepurchaseReminder::find($reminderId);
        
        if ($reminder) {
            $reminder->update([
                'status' => 'pending',
                'next_reminder_date' => now()->addDays($reminder->reminder_interval_days),
            ]);

            session()->flash('success', 'Reminder reset!');
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        $reminders = RepurchaseReminder::with(['customer', 'product', 'lastOrder', 'assignedTo'])
            ->whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterAssignedTo, function ($query) {
                $query->where('assigned_to', $this->filterAssignedTo);
            })
            ->when($this->filterProduct, function ($query) {
                $query->where('product_id', $this->filterProduct);
            })
            ->when(!$user->canManageAllLeads(), function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        $agents = User::whereHas('organizations', function ($q) use ($user) {
            $q->where('organizations.id', $user->current_organization_id);
        })->get();

        $products = Product::where('organization_id', $user->current_organization_id)->get();

        $stats = [
            'total' => RepurchaseReminder::whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })->count(),
            
            'due' => RepurchaseReminder::whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })->dueToday()->count(),
            
            'contacted' => RepurchaseReminder::whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })->where('status', 'contacted')->count(),
            
            'completed' => RepurchaseReminder::whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })->where('status', 'completed')->count(),
        ];

        return view('livewire.repurchase-management', [
            'reminders' => $reminders,
            'agents' => $agents,
            'products' => $products,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}