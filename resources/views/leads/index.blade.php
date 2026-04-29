<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Leads</h1>
        <a href="{{ route('leads.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
            + Tambah Lead
        </a>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('leads.index') }}" class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama, perusahaan, phone, email..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    @foreach(['new','contacted','qualified','proposal','negotiation','won','lost'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sumber</label>
                <select name="source" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Sumber</option>
                    @foreach(['instagram','facebook','google','referral','whatsapp','website','other'] as $src)
                        <option value="{{ $src }}" {{ request('source') == $src ? 'selected' : '' }}>{{ ucfirst($src) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Cari
                </button>
                @if(request('search') || request('status') || request('source'))
                    <a href="{{ route('leads.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Nama</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Perusahaan</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Phone</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Sumber</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Nilai</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leads as $lead)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $lead->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $lead->company ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $lead->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $lead->source ?? '-' }}</td>
                    <td class="px-4 py-3">
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
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$lead->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($lead->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $lead->value ? 'Rp ' . number_format($lead->value, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1">
                            <a href="{{ route('leads.show', $lead) }}"
                               class="bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1 rounded text-xs font-medium hover:bg-blue-100">
                                Lihat
                            </a>
                            <a href="{{ route('leads.edit', $lead) }}"
                               class="bg-yellow-50 text-yellow-600 border border-yellow-200 px-3 py-1 rounded text-xs font-medium hover:bg-yellow-100">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}"
                                  onsubmit="return confirm('Hapus lead ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded text-xs font-medium hover:bg-red-100">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        Belum ada leads. <a href="{{ route('leads.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($leads->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
