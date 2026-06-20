{{-- resources/views/schedule/partials/skeleton.blade.php --}}
<div class="skeleton-wrap" id="skeleton">
    <div class="skel-header">
        <div class="skel-icon"></div>
        <div class="skel-header-text">
            <div class="skel-title"></div>
            <div class="skel-cap"></div>
        </div>
        <div class="skel-actions">
            <div class="skel-btn"></div>
            <div class="skel-btn"></div>
        </div>
    </div>
    <div class="skel-body">
        <div class="skel-table">
            <div class="skel-row skel-header-row">
                <div class="skel-cell skel-cell-time"></div>
                @for($d = 0; $d < 7; $d++)
                    <div class="skel-cell skel-cell-day" style="animation-delay: {{ $d * 50 }}ms"></div>
                @endfor
            </div>
            @for($r = 0; $r < 8; $r++)
            <div class="skel-row">
                <div class="skel-cell skel-cell-time" style="animation-delay: {{ $r * 30 }}ms"></div>
                @for($d = 0; $d < 7; $d++)
                    <div class="skel-cell {{ $r % 2 == 0 ? 'skel-card' : '' }}" style="animation-delay: {{ ($r * 7 + $d) * 20 }}ms"></div>
                @endfor
            </div>
            @endfor
        </div>
    </div>
</div>