<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Order #{{ $order->order_number }}
                </h1>
                <p class="text-sm text-gray-500">
                    Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                </p>
            </div>

            <a href="/orders"
               class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-white border rounded-lg text-sm shadow-sm hover:bg-gray-50">
                ← Back
            </a>
        </div>

        {{-- Top Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <p class="text-xs text-gray-500">Order Status</p>
                <p class="text-lg font-semibold mt-1 capitalize">{{ $order->status }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <p class="text-xs text-gray-500">Payment</p>
                <p class="text-lg font-semibold mt-1 capitalize">{{ $order->payment_status }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-lg font-semibold mt-1">₹{{ number_format($order->total, 2) }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <p class="text-xs text-gray-500">Items</p>
                <p class="text-lg font-semibold mt-1">{{ $order->items->count() }}</p>
            </div>

        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Items --}}
                <div class="bg-white rounded-xl shadow-sm border">
                    <div class="p-4 border-b">
                        <h2 class="font-semibold text-gray-800">Order Items</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">

                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">Product</th>
                                    <th class="px-4 py-3 text-left">Qty</th>
                                    <th class="px-4 py-3 text-left">Price</th>
                                    <th class="px-4 py-3 text-left">Total</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @foreach($order->items as $item)
                                    <tr class="hover:bg-gray-50 transition">

                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-800">
                                                {{ $item->product_name }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                SKU: {{ $item->product_sku }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-sm">
                                            {{ $item->quantity }}
                                        </td>

                                        <td class="px-4 py-3 text-sm">
                                            ₹{{ number_format($item->price, 2) }}
                                        </td>

                                        <td class="px-4 py-3 font-medium">
                                            ₹{{ number_format($item->total, 2) }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

                {{-- Shipment --}}
                @if($shipment)
                    <div class="bg-white rounded-xl shadow-sm border">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h2 class="font-semibold text-gray-800">Shipment</h2>

                            <button wire:click="refreshTracking"
                                class="text-sm px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Refresh
                            </button>
                        </div>

                        <div class="p-4 space-y-2 text-sm">

                            <p><span class="text-gray-500">Provider:</span> {{ $shipment->shippingProvider->name }}</p>
                            <p><span class="text-gray-500">Status:</span> <b>{{ $shipment->status }}</b></p>
                            <p><span class="text-gray-500">Tracking #:</span> {{ $shipment->tracking_number }}</p>

                            @if($shipment->tracking_history)
                                <div class="mt-3 bg-gray-50 p-3 rounded text-xs overflow-auto max-h-40">
                                    {{ $shipment->tracking_history }}
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">

                {{-- Customer --}}
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h2 class="font-semibold text-gray-800 mb-3">Customer</h2>

                    <p class="font-medium">{{ $order->customer->name }}</p>
                    <p class="text-sm text-gray-500">{{ $order->customer->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $order->customer->email }}</p>
                </div>

                {{-- Address --}}
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h2 class="font-semibold text-gray-800 mb-3">Shipping Address</h2>

                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                        {{ $order->shipping_pincode }}<br>
                        {{ $order->shipping_country }}
                    </p>
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h2 class="font-semibold text-gray-800 mb-3">Summary</h2>

                    <div class="space-y-2 text-sm">

                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($order->subtotal,2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Tax</span>
                            <span>₹{{ number_format($order->tax,2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>₹{{ number_format($order->shipping_cost,2) }}</span>
                        </div>

                        <div class="flex justify-between text-red-500">
                            <span>Discount</span>
                            <span>- ₹{{ number_format($order->discount,2) }}</span>
                        </div>

                        <div class="border-t pt-2 flex justify-between font-semibold text-gray-900">
                            <span>Total</span>
                            <span>₹{{ number_format($order->total,2) }}</span>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>