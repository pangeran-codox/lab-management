{{-- resources/views/dev/report-editor-test.blade.php --}}
<x-report-editor
    page-title="Test Editor"
    report-title="Contoh Laporan Test"
    back-url="/"
    site-name="SMKS Nuris Jember"
    site-address="Jl. Contoh No. 1, Jember"
    site-phone="0331-123456"
    :kop-name-size="20"
    :kop-address-size="13"
    :kop-phone-size="12"
>
    <x-slot:info>
        <tr><td style="width:120px">Unit Kerja</td><td>: <strong>Lab Test</strong></td></tr>
        <tr><td>Tanggal Laporan</td><td>: {{ now()->translatedFormat('d F Y') }}</td></tr>
    </x-slot:info>

    <table class="main-table">
        <thead><tr><th>No</th><th>Item</th></tr></thead>
        <tbody>
            <tr><td class="tc">1</td><td>Contoh baris data</td></tr>
            <tr><td class="tc">2</td><td>Baris kedua</td></tr>
        </tbody>
    </table>
</x-report-editor>