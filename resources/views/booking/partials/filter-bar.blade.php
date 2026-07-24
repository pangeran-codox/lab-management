{{--
    Partial: booking/partials/filter-bar.blade.php
    Variabel: $resources
--}}
<div class="filter-bar">
    <form method="GET" action="{{ route('booking.index') }}"
          style="display:flex;flex-wrap:wrap;gap:9px;width:100%;align-items:center">

        {{-- Pertahankan parameter week jika ada --}}
        @if(request('week'))
        <input type="hidden" name="week" value="{{ request('week') }}">
        @endif

        <div class="search-container">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍 Cari nama, kelas, judul..."
                   class="filter-inp" id="booking-search">
            @if(request('search'))
            <span class="search-clear"
                  onclick="document.getElementById('booking-search').value='';this.closest('form').submit()"
                  title="Hapus pencarian">×</span>
            @endif
        </div>

        <div style="flex:0 0 auto">
            <select name="status" class="filter-inp" style="width:auto">
                <option value="all"      {{ request('status','all')==='all'      ?'selected':'' }}>Semua Status</option>
                <option value="pending"  {{ request('status')==='pending'        ?'selected':'' }}>⏳ Pending</option>
                <option value="approved" {{ request('status')==='approved'       ?'selected':'' }}>✓ Disetujui</option>
                <option value="rejected" {{ request('status')==='rejected'       ?'selected':'' }}>✗ Ditolak</option>
            </select>
        </div>

        <div style="flex:0 0 auto">
            <select name="resource_id" class="filter-inp" style="width:auto">
                <option value="">Semua Lab</option>
                @foreach($resources as $r)
                <option value="{{ $r->id }}" {{ request('resource_id')==$r->id ?'selected':'' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex:0 0 auto">
            <input type="date" name="date" value="{{ request('date') }}" class="filter-inp" style="width:auto">
        </div>

        <button type="submit" class="btn-filter">Filter</button>

        @if(request()->hasAny(['search','status','resource_id','date']))
        <a href="{{ route('booking.index', request()->only('week')) }}"
           style="font-size:12px;color:var(--muted);text-decoration:none;padding:4px 8px;font-weight:600">Reset</a>
        @endif
    </form>
</div>
