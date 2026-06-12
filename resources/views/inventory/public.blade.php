{{-- resources/views/inventory/public.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Inventaris Lab')

@section('vite')
@vite(['resources/css/inventory.css', 'resources/js/inventory.js'])
@endsection

@section('content')

    @include('inventory.partials._hero')

    <div class="container anim-body">

        @include('inventory.partials._toolbar')

        @include('inventory.partials._tabs')

        {{-- Skeleton loader --}}
        <div class="skeleton-wrap" id="skeleton">
            <div class="skel-head"></div>
            <div class="skel-body">
                <div class="skel-row">
                    @for($c = 0; $c < 10; $c++)
                        <div class="skel-cell skel-head-cell" style="animation-delay:{{ $c * 30 }}ms"></div>
                    @endfor
                </div>
                @for($r = 0; $r < 6; $r++)
                    <div class="skel-row">
                        @for($c = 0; $c < 10; $c++)
                            <div class="skel-cell" style="animation-delay:{{ ($r * 10 + $c) * 18 }}ms"></div>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>

        {{-- Lab Panels --}}
        @php
            $catLabels  = ['computer'=>'Komputer','peripheral'=>'Peripheral','network'=>'Jaringan','furniture'=>'Furniture','software'=>'Software','other'=>'Lainnya'];
            $catIcons   = ['computer'=>'💻','peripheral'=>'🖱','network'=>'🌐','furniture'=>'🪑','software'=>'💿','other'=>'📦'];
            $condLabels = ['excellent'=>'Sangat Baik','good'=>'Baik','fair'=>'Cukup','poor'=>'Buruk','broken'=>'Rusak'];
        @endphp

        @foreach($resources as $i => $lab)
            @php
                $labItems    = $inventories->where('resource_id', $lab->id)->values();
                $brokenCount = $labItems->sum('quantity_broken');
                $isFirst     = $loop->first;
            @endphp
            @include('inventory.partials._panel', compact('lab', 'labItems', 'brokenCount', 'isFirst', 'catLabels', 'catIcons', 'condLabels'))
        @endforeach

    </div>

    <div class="toast" id="toast">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span id="toast-msg">Berhasil diunduh</span>
    </div>

    <script>
        window.firstLabId = {{ $resources->first()->id ?? 0 }};
        window.currentLab = window.firstLabId;
    </script>

@endsection