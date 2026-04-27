<x-app-layout>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('leads.index') }}" class="text-gray-400 hover:text-gray-600">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Lead Baru</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('leads.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4">

                {{-- Nama --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nama lengkap">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="08xxxxxxxxxx">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="email@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Perusahaan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                    <input type="text" name="company" value="{{ old('company') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nama perusahaan">
                </div>

                {{-- Sumber --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Lead</label>
                    <select name="source" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Sumber --</option>
                        <option value="instagram" {{ old('source') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                        <option value="facebook" {{ old('source') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="google" {{ old('source') == 'google' ? 'selected' : '' }}>Google</option>
                        <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="whatsapp" {{ old('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                        <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ old('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="proposal" {{ old('status') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                        <option value="negotiation" {{ old('status') == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                        <option value="won" {{ old('status') == 'won' ? 'selected' : '' }}>Won</option>
                        <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nilai Deal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Deal (Rp)</label>
                    <input type="number" name="value" value="{{ old('value') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0">
                </div>

                {{-- Kota --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Jakarta">
                </div>

                {{-- Alamat --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Alamat lengkap">
                </div>

                {{-- Catatan --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Simpan Lead
                </button>
                <a href="{{ route('leads.index') }}" class="border border-gray-300 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-50 text-sm">
                    Batal
                </a>
            </div>

        </form>
    </div>
</x-app-layout>