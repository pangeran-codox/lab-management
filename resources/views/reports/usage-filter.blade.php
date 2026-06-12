<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
            📊 Laporan Penggunaan Lab
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <form method="GET" action="{{ route('reports.usage.generate') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Bulan</label>
                        <input type="month" name="month" value="{{ $currentMonth }}" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white p-2.5 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Laboratorium</label>
                        <select name="resource_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white p-2.5 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Semua Laboratorium</option>
                            @foreach($resources as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="include_sunday" value="1"
                                class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/50 dark:peer-focus:ring-primary/50 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Sertakan Hari Minggu</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-5 rounded-lg transition-all duration-300 hover:scale-[1.02] shadow-md active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Buat Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
