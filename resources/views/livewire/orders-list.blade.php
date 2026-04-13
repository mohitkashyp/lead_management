<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">

            <div class="sm:flex sm:items-center sm:justify-between mb-6">

                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Orders
                    </h1>

                    <p class="text-sm text-gray-600">
                        Manage all orders
                    </p>
                </div>

            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer"
                                wire:click="sortBy('order_number')">
                                Order #
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Customer
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Status
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Payment
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Total
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                Date
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                Actions
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $order->order_number }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $order->customer?->name ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    ₹ {{ number_format($order->total, 2) }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm">

                                    <a href="/orders/{{ $order->id }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        View
                                    </a>

                                    <button wire:click="deleteOrder({{ $order->id }})"
                                        class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

                <div class="p-4 border-t">

                    {{ $orders->links() }}

                </div>

            </div>

        </div>
    </div>
</div>
