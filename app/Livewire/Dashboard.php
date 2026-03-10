<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Customer;
use App\Models\RepurchaseReminder;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $stats = [];
    public $recentLeads = [];
    public $followUpToday = [];
    public $overdueFollowUps = [];
    public $repurchaseDue = [];
    public $currentOrganization;

    public function mount()
    {
        $this->currentOrganization = Auth::user()->currentOrganization;
        $this->loadStats();
        $this->loadRecentLeads();
        $this->loadFollowUps();
        $this->loadRepurchaseReminders();
    }

    public function loadStats()
    {
        $user = Auth::user();
        $orgId = $user->current_organization_id;

        $this->stats = [
            'total_leads' => Lead::forOrganization($orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->assignedToMe($user->id);
                })->count(),

            'new_leads' => Lead::forOrganization($orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->assignedToMe($user->id);
                })
                ->whereHas('status', function ($q) {
                    $q->where('name', 'new');
                })->count(),

            'converted_leads' => Lead::forOrganization($orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->assignedToMe($user->id);
                })
                ->whereNotNull('converted_at')->count(),

            'total_orders' => Order::where('organization_id', $orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->where('created_by', $user->id);
                })->count(),

            'orders_this_month' => Order::where('organization_id', $orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->where('created_by', $user->id);
                })
                ->whereMonth('created_at', now()->month)->count(),

            'revenue_this_month' => Order::where('organization_id', $orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->where('created_by', $user->id);
                })
                ->whereMonth('created_at', now()->month)
                ->where('payment_status', 'paid')
                ->sum('total'),

            'total_customers' => Customer::where('organization_id', $orgId)->count(),

            'follow_ups_today' => Lead::forOrganization($orgId)
                ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                    return $q->assignedToMe($user->id);
                })
                ->followUpToday()->count(),
        ];
    }

    public function loadRecentLeads()
    {
        $user = Auth::user();
        $orgId = $user->current_organization_id;

        $this->recentLeads = Lead::with(['source', 'status', 'assignedTo'])
            ->forOrganization($orgId)
            ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                return $q->assignedToMe($user->id);
            })
            ->latest()
            ->limit(5)
            ->get();
    }

    public function loadFollowUps()
    {
        $user = Auth::user();
        $orgId = $user->current_organization_id;

        $this->followUpToday = Lead::with(['source', 'status', 'assignedTo'])
            ->forOrganization($orgId)
            ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                return $q->assignedToMe($user->id);
            })
            ->followUpToday()
            ->get();

        $this->overdueFollowUps = Lead::with(['source', 'status', 'assignedTo'])
            ->forOrganization($orgId)
            ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                return $q->assignedToMe($user->id);
            })
            ->overdue()
            ->get();
    }

    public function loadRepurchaseReminders()
    {
        $user = Auth::user();

        $this->repurchaseDue = RepurchaseReminder::with(['customer', 'product'])
            ->whereHas('customer', function ($q) use ($user) {
                $q->where('organization_id', $user->current_organization_id);
            })
            ->when(!$user->canManageAllLeads(), function ($q) use ($user) {
                return $q->where('assigned_to', $user->id);
            })
            ->dueToday()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}