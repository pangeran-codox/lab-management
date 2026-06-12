{{-- resources/views/reports/public.blade.php --}}
@extends('layouts.public-schedule')

@section('title', 'Laporan Publik')

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="hero">
    <h1>📋 Laporan Publik</h1>
    <p>Akses laporan inventaris dan penggunaan laboratorium</p>
</div>

{{-- ═══ CARDS ═══ --}}
<div class="wrap" style="max-width:900px">
    <div class="report-cards-grid">
        
        {{-- Inventaris --}}
        <a href="{{ route('inventory.public') }}" class="report-card">
            <div class="report-icon">📦</div>
            <div class="report-title">Laporan Inventaris</div>
            <div class="report-desc">Lihat dan unduh daftar barang laboratorium</div>
            <div class="report-actions">
                <span class="report-btn">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Lihat Laporan
                </span>
            </div>
        </a>

        {{-- Rekap Penggunaan --}}
        <a href="{{ route('rekap.public') }}" class="report-card">
            <div class="report-icon">📊</div>
            <div class="report-title">Rekap Penggunaan Lab</div>
            <div class="report-desc">Lihat statistik dan kalender penggunaan lab</div>
            <div class="report-actions">
                <span class="report-btn">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Lihat Laporan
                </span>
            </div>
        </a>

    </div>
</div>

<style>
    .report-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 32px;
    }
    .report-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0,105,62,.06);
        border: 1px solid #e6f0e8;
        text-decoration: none;
        transition: transform .2s, box-shadow .2s;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(0,105,62,.1);
    }
    .report-icon {
        font-size: 44px;
        line-height: 1;
    }
    .report-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 18px;
        color: #1A2517;
    }
    .report-desc {
        color: #6b8fa3;
        font-size: 13px;
        line-height: 1.5;
    }
    .report-actions {
        margin-top: auto;
        padding-top: 12px;
    }
    .report-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #003d24;
        color: white;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 18px;
        border-radius: 10px;
    }
    @media (max-width: 640px) {
        .report-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection