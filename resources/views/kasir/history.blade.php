<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Riwayat Transaksi</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="dashboard-shell">
        <div class="min-h-screen xl:grid xl:grid-cols-[280px_minmax(0,1fr)]">
            @include('layout.kasir-sidebar')

            <main class="min-w-0 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto flex max-w-7xl flex-col gap-6">
                    @include('layout.kasir-topbar')

                    <section class="dashboard-panel p-5 sm:p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Riwayat Transaksi</h2>
                                <p class="text-sm text-slate-500 mt-1">Daftar semua transaksi yang telah selesai</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-4 font-semibold w-[15%]">Order ID</th>
                                        <th class="px-5 py-4 font-semibold w-[20%]">Waktu</th>
                                        <th class="px-5 py-4 font-semibold w-[15%]">Total Item</th>
                                        <th class="px-5 py-4 font-semibold w-[20%]">Total Tagihan</th>
                                        <th class="px-5 py-4 font-semibold w-[15%]">Pembayaran</th>
                                        <th class="px-5 py-4 font-semibold w-[15%]">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse($orders as $order)
                                    @php
                                        // Support backward compatibility structure
                                        $id = is_array($order) ? $order['order_number'] : $order->order_number;
                                        
                                        if (is_array($order)) {
                                            $date = isset($order['paid_at']) ? \Carbon\Carbon::parse($order['paid_at'])->format('d M Y, H:i') : '-';
                                        } else {
                                            $date = $order->paid_at ? $order->paid_at->format('d M Y, H:i') : $order->created_at->format('d M Y, H:i');
                                        }
                                        
                                        $items = is_array($order) ? collect($order['items']) : $order->items;
                                        $totalItems = $items->sum('qty');
                                        $totalPrice = is_array($order) ? $order['total'] : $order->total;
                                        $status = is_array($order) ? $order['status'] : $order->status;
                                        $paymentMethod = is_array($order) ? ($order['payment_method'] ?? 'Tunai') : ($order->payment_method ?? 'Tunai');
                                        
                                        $statusConfig = [
                                            'paid' => ['label' => 'Lunas', 'class' => 'bg-[#e3f1df] text-[#588142] border-[#c3e3ba]'],
                                            'pending' => ['label' => 'Pending', 'class' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                        ];
                                        
                                        $currentStatus = $statusConfig[$status] ?? $statusConfig['paid'];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-5 py-4 font-semibold text-slate-900 border-b border-slate-100 max-w-[150px] truncate" title="{{ $id }}">{{ $id }}</td>
                                        <td class="px-5 py-4 font-medium text-slate-500 border-b border-slate-100 whitespace-nowrap">{{ $date }}</td>
                                        <td class="px-5 py-4 border-b border-slate-100">
                                            <span class="inline-flex h-7 items-center justify-center rounded-lg bg-slate-100 px-3 text-xs font-semibold text-slate-700">
                                                {{ $totalItems }} Item
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 font-bold text-kasir-primary border-b border-slate-100">Rp {{ number_format($totalPrice, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4 border-b border-slate-100">
                                            @if(strtolower($paymentMethod) === 'qris')
                                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                    QRIS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                    Tunai
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 border-b border-slate-100">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border {{ $currentStatus['class'] }}">
                                                {{ $currentStatus['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10">
                                            <div class="flex flex-col items-center justify-center text-slate-400">
                                                <svg class="h-10 w-10 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <p class="text-[15px] font-medium">Belum ada transaksi</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
