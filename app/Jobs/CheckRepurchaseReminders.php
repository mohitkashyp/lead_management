<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\RepurchaseReminder;
use Carbon\Carbon;

class CheckRepurchaseReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all completed orders from the last 90 days
        $orders = Order::with(['customer', 'items.product'])
            ->where('status', 'delivered')
            ->where('delivered_at', '>=', now()->subDays(90))
            ->get();

        foreach ($orders as $order) {
            // For each product in the order, create or update repurchase reminder
            foreach ($order->items as $item) {
                // Check if product has a typical repurchase cycle
                $repurchaseInterval = $this->getRepurchaseInterval($item->product);
                
                if (!$repurchaseInterval) {
                    continue;
                }

                // Check if reminder already exists
                $existingReminder = RepurchaseReminder::where('customer_id', $order->customer_id)
                    ->where('product_id', $item->product_id)
                    ->where('last_order_id', $order->id)
                    ->first();

                if (!$existingReminder) {
                    // Create new reminder
                    RepurchaseReminder::create([
                        'customer_id' => $order->customer_id,
                        'last_order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'last_purchase_date' => $order->delivered_at,
                        'next_reminder_date' => Carbon::parse($order->delivered_at)->addDays($repurchaseInterval),
                        'reminder_interval_days' => $repurchaseInterval,
                        'status' => 'pending',
                        'assigned_to' => $order->created_by,
                    ]);
                }
            }
        }

        // Process pending reminders that are due
        $dueReminders = RepurchaseReminder::with(['customer', 'product', 'assignedTo'])
            ->dueToday()
            ->get();

        foreach ($dueReminders as $reminder) {
            // Here you can send notifications to assigned agents
            // For example: send email, SMS, or create a lead
            
            // You could automatically create a new lead
            // $this->createLeadFromReminder($reminder);
            
            // Or send notification to agent
            // $reminder->assignedTo->notify(new RepurchaseReminderNotification($reminder));
            
            // Mark as sent
            $reminder->markAsSent();
        }
    }

    /**
     * Get repurchase interval for a product
     * This can be customized based on product category or custom fields
     */
    protected function getRepurchaseInterval($product): ?int
    {
        // Default intervals by category (in days)
        $intervals = [
            'consumables' => 30,
            'supplements' => 30,
            'cosmetics' => 60,
            'office_supplies' => 90,
        ];

        // Get product category
        if ($product && $product->category) {
            $categorySlug = $product->category->slug;
            return $intervals[$categorySlug] ?? null;
        }

        // Default for all products without specific category
        return 30;
    }

    /**
     * Create a lead from repurchase reminder (optional)
     */
    protected function createLeadFromReminder(RepurchaseReminder $reminder): void
    {
        // Implementation to create a lead automatically
        // This can be enabled based on business requirements
    }
}