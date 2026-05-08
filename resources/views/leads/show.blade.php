<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('leads.index') }}" class="text-gray-400 hover:text-gray-600">← Kembali</a>
            <h1 class="text-2xl font-bold text-gray-800">{{ $lead->name }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('leads.edit', $lead) }}"
               class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 text-sm font-medium">Edit</a>
            <form method="POST" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('Hapus lead ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm font-medium">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Informasi Lead</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Nama</p>
                    <p class="font-medium text-gray-800">{{ $lead->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Perusahaan</p>
                    <p class="font-medium text-gray-800">{{ $lead->company ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">No. HP</p>
                    <p class="font-medium text-gray-800">{{ $lead->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Email</p>
                    <p class="font-medium text-gray-800">{{ $lead->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Kota</p>
                    <p class="font-medium text-gray-800">{{ $lead->city ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Sumber</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($lead->source ?? '-') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Alamat</p>
                    <p class="font-medium text-gray-800">{{ $lead->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">WA Phone</p>
                    <p class="font-medium text-gray-800">{{ $lead->wa_phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Ketertarikan Produk</p>
                    <p class="font-medium text-gray-800">
                        @if($lead->product)
                            <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded text-xs">
                                {{ $lead->product->name }}
                            </span>
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-400">Catatan Ketertarikan</p>
                    <p class="font-medium text-gray-800">{{ $lead->interest_notes ?? '-' }}</p>
                </div>
            </div>

            @if($lead->notes)
            <div class="mt-4 pt-4 border-t">
                <p class="text-gray-400 text-sm mb-1">Catatan</p>
                <p class="text-sm text-gray-700">{{ $lead->notes }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-2">Status</p>
                @php
                    $colors = [
                        'new'         => 'bg-blue-100 text-blue-700',
                        'contacted'   => 'bg-yellow-100 text-yellow-700',
                        'qualified'   => 'bg-purple-100 text-purple-700',
                        'proposal'    => 'bg-orange-100 text-orange-700',
                        'negotiation' => 'bg-pink-100 text-pink-700',
                        'won'         => 'bg-green-100 text-green-700',
                        'lost'        => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colors[$lead->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ ucfirst($lead->status) }}
                </span>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-1">Nilai Deal</p>
                <p class="text-xl font-bold text-gray-800">
                    {{ $lead->value ? 'Rp ' . number_format($lead->value, 0, ',', '.') : '-' }}
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-1">Ditambahkan</p>
                <p class="text-sm font-medium text-gray-800">{{ $lead->created_at->format('d M Y') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-2">Hubungi via WhatsApp</p>
                @if($lead->wa_phone)
                    <a href="https://wa.me/{{ $lead->wa_phone }}" target="_blank"
                       class="block w-full bg-green-500 text-white text-center px-4 py-2 rounded-lg hover:bg-green-600 text-sm font-medium">
                        💬 Buka WhatsApp
                    </a>
                @else
                    <p class="text-xs text-gray-400">Nomor WA tidak tersedia</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
