<x-app-layout>
<x-slot name="title">Jadwal Penting</x-slot>

<style>
.table-row { border-top: 1px solid #f0f4ee; transition: background .15s; }
.table-row:hover { background: #f8faf7; }
.badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700; }
.btn-danger { background:#fee2e2;color:#991b1b;border:none;border-radius:8px;padding:5px 12px;font-size:11px;font-weight:700;cursor:pointer;transition:background .15s; }
.btn-danger:hover { background:#fecaca; }
</style>

{{-- HEADER --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <div>
        <h1 style="font-family:Outfit,sans-serif;font-weight:800;font-size:22px;color:#1A2517;margin:0">📌 Jadwal Penting</h1>
        <p style="font-size:13px;color:#9ca3af;margin:4px 0 0">Kelola event khusus yang memblokir booking lab</p>
    </div>
    <a href="{{ route('important-schedule.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none">
        + Tambah Jadwal Penting
    </a>
</div>

{{-- FILTER --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;padding:16px 20px;margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div style="flex:1;min-width:180px">
            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px">Lab</label>
            <select name="resource_id" class="input-modern" onchange="this.form.submit()">
                <option value="">Semua Lab</option>
                @foreach($resources as $r)
                <option value="{{ $r->id }}" {{ request('resource_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:180px">
            <label style="font-size:11px;font-weight:700;color:#6b7280;display:block;margin-bottom:4px">Bulan</label>
            <input type="month" name="month" value="{{ request('month') }}" class="input-modern" onchange="this.form.submit()">
        </div>
        @if(request()->hasAny(['resource_id','month']))
        <a href="{{ route('important-schedule.index') }}"
           style="font-size:12px;color:#9ca3af;text-decoration:none;padding:8px 14px;border:1.5px solid #e8f0e6;border-radius:10px;font-weight:600">
            Reset
        </a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e8f0e6;box-shadow:0 1px 4px rgba(26,37,23,.05);overflow:hidden">
    <div style="padding:14px 20px;border-bottom:1px solid #f0f4ee;display:flex;align-items:center;justify-content:space-between">
        <h2 style="font-family:Outfit,sans-serif;font-weight:700;color:#1A2517;font-size:14px;margin:0">
            Daftar Event
            <span style="background:#f0f4ee;color:#6b7280;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;margin-left:6px">
                {{ $importantSchedules->total() }}
            </span>
        </h2>
    </div>

    @forelse($importantSchedules as $item)
    <div class="table-row" style="padding:14px 20px;display:flex;align-items:center;gap:14px;flex-wrap:wrap">

        {{-- Color dot --}}
        <div style="width:12px;height:12px;border-radius:50%;background:{{ $item->color }};flex-shrink:0"></div>

        {{-- Info --}}
        <div style="flex:1;min-width:200px">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <p style="font-size:14px;font-weight:700;color:#1A2517;margin:0">{{ $item->title }}</p>
                <span style="background:#f0f4ee;color:#374151;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px">
                    {{ $item->type_label }}
                </span>
                @if($item->is_full_day)
                <span style="background:#ede9fe;color:#6d28d9;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px">
                    Seharian
                </span>
                @endif
            </div>
            <p style="font-size:12px;color:#9ca3af;margin:4px 0 0">
                {{ $item->resource->name ?? '-' }} ·
                {{ $item->date->translatedFormat('l, d F Y') }}
                @if(!$item->is_full_day && $item->startSlot && $item->endSlot)
                · {{ $item->startSlot->name }} – {{ $item->endSlot->name }}
                ({{ $item->startSlot->start_time }} – {{ $item->endSlot->end_time }})
                @endif
            </p>
            @if($item->description)
            <p style="font-size:11px;color:#b0b8a8;margin:3px 0 0;font-style:italic">{{ Str::limit($item->description, 80) }}</p>
            @endif
        </div>

        {{-- Status: lewat / akan datang --}}
        @if($item->date->isPast())
        <span style="background:#f3f4f6;color:#9ca3af;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;flex-shrink:0">Lewat</span>
        @else
        <span style="background:#dcfce7;color:#166534;font-size:10px;font-weight:700;padding:3px 10px;border-radius:999px;flex-shrink:0">
            {{ $item->date->diffForHumans() }}
        </span>
        @endif

        {{-- Actions --}}
        <div style="display:flex;gap:8px;flex-shrink:0">
            <a href="{{ route('important-schedule.edit', $item) }}"
               style="background:#f0f4ee;color:#1A2517;border-radius:8px;padding:6px 12px;font-size:11px;font-weight:700;text-decoration:none">
                ✏️ Edit
            </a>
            <form method="POST" action="{{ route('important-schedule.destroy', $item) }}"
                  onsubmit="return confirm('Hapus jadwal penting ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">🗑 Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:48px;text-align:center;color:#9ca3af">
        <div style="font-size:36px;margin-bottom:10px">📭</div>
        <p style="font-size:14px;font-weight:600;margin:0">Belum ada jadwal penting</p>
        <p style="font-size:12px;margin:6px 0 0">Tambahkan event seperti ujian atau olimpiade yang memblokir booking lab.</p>
        <a href="{{ route('important-schedule.create') }}"
           style="display:inline-block;margin-top:14px;background:linear-gradient(135deg,#1A2517,#2d3d29);color:#ACC8A2;padding:8px 18px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none">
            + Tambah Sekarang
        </a>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($importantSchedules->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f0f4ee">
        {{ $importantSchedules->withQueryString()->links() }}
    </div>
    @endif
</div>

</x-app-layout>