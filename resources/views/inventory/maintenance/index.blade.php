<x-app-layout>
    <x-slot name="title">Log Perbaikan Inventaris</x-slot>

    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Perbaikan Barang</h2>
            <a href="{{ route('inventory.admin') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Kembali ke Inventaris
            </a>
        </div>

        {{-- Tabel Log --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-bottom border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Barang / Lab</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Jenis / Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Biaya</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $log->maintenance_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $log->inventory->item_name }}</div>
                                <div class="text-xs text-gray-500">{{ $log->inventory->resource->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700">
                                    {{ $log->maintenance_type }}
                                </span>
                                <div class="mt-1 text-xs text-gray-500">
                                    Status: <span class="font-semibold">{{ $log->status }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($log->description, 50) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                Rp {{ number_format($log->cost, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('inventory.maintenance.destroy', $log) }}" method="POST" onsubmit="return confirm('Hapus log ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Belum ada riwayat perbaikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
