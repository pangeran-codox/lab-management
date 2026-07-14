{{-- resources/views/schedule/index.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Jadwal Lab')

@section('vite')
{{-- Data dari server untuk schedule.js --}}
<script>
    window.ALL_SLOTS = @json($timeSlots->where('is_break', false)->values());
    window.TEACHERS  = @json($teachers);
    window.BOOKINGS_THIS_WEEK = @json($bookings);
    window.SUNDAY_BOOKINGS_THIS_WEEK = @json($sundayBookings);
</script>
@vite(['resources/js/app.js', 'resources/css/schedule.css', 'resources/css/schedule-cards.css', 'resources/js/schedule.js'])
@endsection

@section('content')

{{-- ═══ HERO ═══ --}}
@include('schedule.partials.hero')

{{-- ═══ MAIN ═══ --}}
<div class="main">

    {{-- Banner booking ditutup --}}
    @if(!\App\Models\Setting::isEnabled(\App\Models\Setting::BOOKING_OPEN))
    <div class="flash flash-err" style="display:flex;align-items:center;gap:10px;background:#fef9c3;border-color:#fde047;color:#713f12">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#ca8a04">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <strong>Booking sedang ditutup.</strong>
            Mohon hubungi admin untuk informasi lebih lanjut.
        </div>
    </div>
    @endif

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flash flash-ok">
        ✓ {{ session('success') }}
        <div style="margin-top:6px;font-size:12px;font-weight:400;color:#166534;">
            Pantau status booking di halaman jadwal. Slot yang sudah diajukan akan tampil dengan warna kuning (Pending).
        </div>
    </div>
    @endif
    @if($errors->has('error'))
    <div class="flash flash-err">⚠ {{ $errors->first('error') }}</div>
    @endif

    {{-- Week navigation --}}
    @include('schedule.partials.week-nav')

    {{-- Tabs lab --}}
    @include('schedule.partials.tabs')

    {{-- Skeleton loader --}}
    @include('schedule.partials.skeleton')

    {{-- Tabel jadwal per lab --}}
    @include('schedule.partials.panels')

</div>

{{-- ═══ MODALS ═══ --}}
@include('schedule.partials.modal-detail')
@include('schedule.partials.modal-sunday')
@include('schedule.partials.modal-booking')

@endsection