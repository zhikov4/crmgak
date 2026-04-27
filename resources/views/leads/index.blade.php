<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Leads</h1>
        <a href="{{ route('leads.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
            + Tambah Lead
        </a>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
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
                        <div class="flex gap-2">
                            <a href="{{ route('leads.show', $lead) }}" 
                               class="text-blue-600 hover:underline text-xs">Lihat</a>
                            <a href="{{ route('leads.edit', $lead) }}" 
                               class="text-yellow-600 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}" 
                                  onsubmit="return confirm('Hapus lead ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs">Hapus</button>
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

        {{-- Pagination --}}
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</x-app-layout>