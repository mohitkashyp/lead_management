<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterPaymentStatus = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPaymentStatus' => ['except' => ''],
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
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }
    }

    public function clearFilters()
    {
        $this->reset(['search','filterStatus','filterPaymentStatus']);
        $this->resetPage();
    }

    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if ($order) {
            $order->delete();
            session()->flash('success','Order deleted successfully');
        }
    }

    public function render()
    {
        $orders = Order::with([
                'customer',
                'lead',
                'createdBy'
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number','like','%'.$this->search.'%')
                      ->orWhereHas('customer', function ($q) {
                          $q->where('name','like','%'.$this->search.'%');
                      });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status',$this->filterStatus);
            })
            ->when($this->filterPaymentStatus, function ($query) {
                $query->where('payment_status',$this->filterPaymentStatus);
            })
            ->orderBy($this->sortField,$this->sortDirection)
            ->paginate(15);

        $statuses = [
            'pending',
            'confirmed',
            'shipped',
            'delivered'
        ];

        $paymentStatuses = [
            'pending',
            'paid',
            'failed'
        ];

        return view('livewire.orders-list',[
            'orders'=>$orders,
            'statuses'=>$statuses,
            'paymentStatuses'=>$paymentStatuses
        ])->layout('layouts.app');
    }
}