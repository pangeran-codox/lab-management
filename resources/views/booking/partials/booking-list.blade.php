{{--
    Partial: booking/partials/booking-list.blade.php
    Variabel: $bookings, $sundayBookings, $stats, $resources
--}}

{{-- ─── SUNDAY BOOKINGS ─── --}}
@if($sundayBookings->total() > 0)
<div class="table-card" style="margin-bottom:28px;border:2px solid #3b82f6;box-shadow:0 10px 25px rgba(59,130,246,.12)">
    <div class="table-head-bar" style="background:#3b82f6;color:#fff;padding:12px 20px">
        <div>
            <span class="table-title" style="color:#fff">📅 Permintaan Booking Minggu (Full Day)</span>
            <span class="table-count" style="color:rgba(255,255,255,.8)">&nbsp;({{ $sundayBookings->total() }} data)</span>
        </div>
        <span class="badge" style="background:rgba(255,255,255,.2);color:#fff">KHUSUS MINGGU</span>
    </div>
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th style="background:#f8fafb">Tanggal &amp; Lab</th>
                    <th style="background:#f8fafb">Pemohon</th>
                    <th style="background:#f8fafb">Kegiatan</th>
                    <th style="background:#f8fafb" class="th-center">Status</th>
                    <th style="background:#f8fafb" class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sundayBookings as $sb)
                <tr class="{{ $sb->status === 'pending' ? 'row-pending' : ($sb->status === 'approved' ? 'row-approved' : 'row-rejected') }}">
                    <td>
                        <div class="cell-primary">{{ $sb->booking_date->translatedFormat('d M Y') }}</div>
                        <div class="cell-accent" style="color:#2563eb">🖥 {{ $sb->resource->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $sb->teacher_name }}</div>
                        <div class="cell-secondary">{{ $sb->organization->name ?? '-' }}</div>
                        @if($sb->teacher_phone)
                        <div class="cell-secondary" style="display:flex;align-items:center;gap:8px">
                            📱 {{ substr(preg_replace('/\D/', '', $sb->teacher_phone), 0, 3) }}****{{ substr(preg_replace('/\D/', '', $sb->teacher_phone), -2) }}
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $sb->teacher_phone) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;background:#25d366;color:#fff;border-radius:6px;text-decoration:none;font-weight:700;font-size:11px">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.52 3.48a11.85 11.85 0 00-17 0 11.89 11.89 0 00-2.74 8.46 11.93 11.93 0 003.56 8.29l.17.18-.4 1.46-1.47.4.18.18a11.92 11.92 0 008.68 3.04h.01a11.94 11.94 0 008.46-3.05 11.89 11.89 0 003.05-8.46 11.85 11.85 0 00-3.48-8.46zM17.86 15.9c-.33.93-1.91 1.78-2.65 1.8-.68.02-1.55.04-2.51-.16-.58-.12-1.32-.42-2.27-.88-3.96-1.93-6.53-6.3-6.7-6.6-.17-.3-1.4-2.33 1.41-4.48 1.25-.97 2.5-1.13 3.04-1.13.47 0 1.09-.18 1.68.9.59 1.08.79 1.87 1.01 2.35.22.48.11.9-.06 1.28-.18.38-.5.61-.93.97-.43.36-.75.54-1.07.72-.32.18-.07.86.43 1.89.5 1.03 1.03 1.69 1.86 2.24 1.22.8 2.22.7 2.89.62.67-.08 2.08-.85 2.38-1.68.3-.83.3-1.54.21-1.54-.08-.13-.28-.2-.6-.32-.32-.16-1.9-.93-2.19-1.04-.29-.11-.5-.16-.72.11-.22.27-.84.98-1.03 1.18-.19.2-.38.22-.7.08-.32-.14-1.34-.49-2.55-1.57-.94-.84-1.57-1.88-1.76-2.2-.19-.32-.02-.49.14-.64.14-.14.33-.36.5-.54.17-.18.27-.3.4-.5.13-.2.06-.37-.03-.52-.09-.15-.79-1.9-1.08-2.58-.29-.68-.58-.58-.72-.59-.12 0-.26 0-.4.06-.14.06-.36.14-.55.42-.19.28-.73.71-.73 1.73 0 1.01.75 1.99.86 2.13.11.14 1.58 2.42 3.82 3.41.54.24 1.04.37 1.49.48.7.17 1.34.14 1.84.09.59-.06 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.28-.2-.6-.32z"/>
                                </svg>
                                Chat
                            </a>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="cell-primary">{{ $sb->class_name }}</div>
                        <div class="cell-secondary">{{ $sb->title }}</div>
                    </td>
                    <td class="td-center">
                        <span class="badge badge-{{ $sb->status }}">
                            @if($sb->status==='pending')<span class="dot"></span>@endif
                            {{ ucfirst($sb->status) }}
                        </span>
                    </td>
                    <td class="td-center">
                        <div class="act-wrap">
                            @if($sb->status === 'pending')
                                <form method="POST" action="{{ route('booking.approve.sunday', $sb->id) }}"
                                      onsubmit="return confirm('Setujui booking minggu ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-approve-single" style="background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff">✓ Setujui</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openReject({{ $sb->id }}, '{{ addslashes($sb->title) }}', '{{ addslashes($sb->teacher_name) }}', 'sunday')">
                                    ✗ Tolak
                                </button>
                            @endif
                            <form method="POST" action="{{ route('booking.destroy.sunday', $sb->id) }}"
                                  onsubmit="return confirm('Hapus booking minggu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($sundayBookings->hasPages())
    <div class="pagi-wrap" style="background:#fff">{{ $sundayBookings->links() }}</div>
    @endif
</div>
@endif

{{-- ─── DAFTAR BOOKING (tabel list) ─── --}}
<div class="table-card">
    <div class="table-head-bar">
        <div>
            <span class="table-title">Daftar Semua Booking</span>
            @if($bookings->total() > 0)
            <span class="table-count">&nbsp;({{ $bookings->total() }} data)</span>
            @endif
        </div>
        @if($stats['regular']['pending'] > 0)
        <span class="badge badge-pending">
            <span class="dot"></span>
            {{ $stats['regular']['pending'] }} menunggu persetujuan
        </span>
        @endif
    </div>

    @if($bookings->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📋</div>
        Belum ada booking ditemukan
    </div>
    @else
    <div class="tbl-scroll">
        <table>
            <thead>
                <tr>
                    <th>Tanggal &amp; Lab</th>
                    <th>Pemohon</th>
                    <th>Kegiatan</th>
                    <th>Slot Waktu</th>
                    <th class="th-center">Status</th>
                    <th class="th-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                @php
                    $rowClass   = match($b->status) { 'pending' => 'row-pending', 'approved' => 'row-approved', default => 'row-rejected' };
                    $groupCount = $b->status === 'pending'
                        ? $bookings->where('teacher_name', $b->teacher_name)
                            ->where('resource_id', $b->resource_id)
                            ->where('booking_date', $b->booking_date)
                            ->where('status', 'pending')->count()
                        : 0;
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>
                        <div class="cell-primary">{{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}</div>
                        <div class="cell-accent">🖥 {{ $b->resource->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $b->teacher_name }}</div>
                        <div class="cell-secondary">{{ $b->class_name }}</div>
                        @if($b->teacher_phone)
                        <div class="cell-secondary" style="display:flex;align-items:center;gap:8px">
                            📱 {{ substr(preg_replace('/\D/', '', $b->teacher_phone), 0, 3) }}****{{ substr(preg_replace('/\D/', '', $b->teacher_phone), -2) }}
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $b->teacher_phone) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;padding:4px 8px;background:#25d366;color:#fff;border-radius:6px;text-decoration:none;font-weight:700;font-size:11px">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.52 3.48a11.85 11.85 0 00-17 0 11.89 11.89 0 00-2.74 8.46 11.93 11.93 0 003.56 8.29l.17.18-.4 1.46-1.47.4.18.18a11.92 11.92 0 008.68 3.04h.01a11.94 11.94 0 008.46-3.05 11.89 11.89 0 003.05-8.46 11.85 11.85 0 00-3.48-8.46zM17.86 15.9c-.33.93-1.91 1.78-2.65 1.8-.68.02-1.55.04-2.51-.16-.58-.12-1.32-.42-2.27-.88-3.96-1.93-6.53-6.3-6.7-6.6-.17-.3-1.4-2.33 1.41-4.48 1.25-.97 2.5-1.13 3.04-1.13.47 0 1.09-.18 1.68.9.59 1.08.79 1.87 1.01 2.35.22.48.11.9-.06 1.28-.18.38-.5.61-.93.97-.43.36-.75.54-1.07.72-.32.18-.07.86.43 1.89.5 1.03 1.03 1.69 1.86 2.24 1.22.8 2.22.7 2.89.62.67-.08 2.08-.85 2.38-1.68.3-.83.3-1.54.21-1.54-.08-.13-.28-.2-.6-.32-.32-.16-1.9-.93-2.19-1.04-.29-.11-.5-.16-.72.11-.22.27-.84.98-1.03 1.18-.19.2-.38.22-.7.08-.32-.14-1.34-.49-2.55-1.57-.94-.84-1.57-1.88-1.76-2.2-.19-.32-.02-.49.14-.64.14-.14.33-.36.5-.54.17-.18.27-.3.4-.5.13-.2.06-.37-.03-.52-.09-.15-.79-1.9-1.08-2.58-.29-.68-.58-.58-.72-.59-.12 0-.26 0-.4.06-.14.06-.36.14-.55.42-.19.28-.73.71-.73 1.73 0 1.01.75 1.99.86 2.13.11.14 1.58 2.42 3.82 3.41.54.24 1.04.37 1.49.48.7.17 1.34.14 1.84.09.59-.06 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.28-.2-.6-.32z"/>
                                </svg>
                                Chat
                            </a>
                        </div>
                        @endif
                    </td>
                    <td style="max-width:190px">
                        <div class="cell-primary" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $b->title }}</div>
                        <div class="cell-secondary">
                            {{ $b->subject_name ?? '-' }}
                            @if($b->participant_count)
                            · <span style="font-weight:600;color:#6b7280">{{ $b->participant_count }} peserta</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($b->timeSlot)
                        <div class="cell-slot">{{ $b->timeSlot->name }}</div>
                        <div class="cell-time">{{ \Carbon\Carbon::parse($b->timeSlot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($b->timeSlot->end_time)->format('H:i') }}</div>
                        @else
                        <span style="color:#d1d5db">—</span>
                        @endif
                    </td>
                    <td class="td-center">
                        @if($b->status === 'pending')
                            <span class="badge badge-pending"><span class="dot"></span>Pending</span>
                        @elseif($b->status === 'approved')
                            <span class="badge badge-approved">✓ Disetujui</span>
                            @if($b->approved_at)
                            <div style="font-size:10px;color:var(--muted);margin-top:3px">{{ \Carbon\Carbon::parse($b->approved_at)->format('d/m H:i') }}</div>
                            @endif
                        @else
                            <span class="badge badge-rejected">✗ Ditolak</span>
                        @endif
                    </td>
                    <td class="td-center">
                        <div class="act-wrap">
                            @if($b->status === 'pending')
                                @if($groupCount > 1)
                                <form method="POST" action="{{ route('booking.approve.group') }}"
                                      onsubmit="return confirm('Setujui semua {{ $groupCount }} slot booking {{ $b->teacher_name }} sekaligus?')">
                                    @csrf
                                    <input type="hidden" name="teacher_name" value="{{ $b->teacher_name }}">
                                    <input type="hidden" name="resource_id"  value="{{ $b->resource_id }}">
                                    <input type="hidden" name="booking_date" value="{{ $b->booking_date }}">
                                    <button type="submit" class="btn-approve-group">✓ {{ $groupCount }} Slot</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openRejectGroup('{{ addslashes($b->teacher_name) }}', {{ $b->resource_id }}, '{{ $b->booking_date }}', {{ $groupCount }})">
                                    ✗ {{ $groupCount }} Slot
                                </button>
                                @else
                                <form method="POST" action="{{ route('booking.approve', $b->id) }}"
                                      onsubmit="return confirm('Setujui booking ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-approve-single">✓ Setujui</button>
                                </form>
                                <button class="btn-reject-act"
                                    onclick="openReject({{ $b->id }}, '{{ addslashes($b->title) }}', '{{ addslashes($b->teacher_name) }}')">
                                    ✗ Tolak
                                </button>
                                @endif
                            @else
                                <a href="{{ route('booking.show', $b->id) }}" class="btn-detail">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            @endif
                            <form method="POST" action="{{ route('booking.destroy', $b->id) }}"
                                  onsubmit="return confirm('Hapus booking ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="pagi-wrap">{{ $bookings->links() }}</div>
    @endif
    @endif
</div>
