<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Cetakan Butiran Aset</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; }
        .header { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid black; padding-bottom: 12px; margin-bottom: 18px; }
        .header img { width: 64px; }
        .header-text { flex: 1; text-align: center; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .sub-title { font-size: 13px; margin-top: 3px; }
        .request-block { margin-bottom: 24px; break-inside: auto; page-break-inside: auto; }
        .request-meta { margin-bottom: 10px; font-size: 12px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        table, th, td { border: 1px solid black; }
        th { background-color: #e5e7eb; }
        th, td { padding: 6px; text-align: center; vertical-align: top; }
        tr { break-inside: avoid; page-break-inside: avoid; }
        .text-left { text-align: left; }
        .total-row { background-color: #f3f4f6; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 12px; }
        .signature { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature div { text-align: center; width: 30%; }
        .label { font-weight: bold; }
        .muted { color: #4b5563; }
        @media print { @page { size: A4 {{ $orientation === 'portrait' ? 'portrait' : 'landscape' }}; margin: 15mm; } }
    </style>
</head>
<body>
@php
    $grandTotal = 0;
@endphp
<div class="header">
    <img src="{{ asset('images/logo.png') }}">
    <div class="header-text">
        <div class="title">Butiran Anggaran Perbelanjaan dan Ulasan</div>
        <div class="sub-title">Kod {{ $assetMainCategory['code'] }} - {{ $assetMainCategory['label'] }}</div>
        <div class="sub-title">Jabatan: {{ $departmentCode }} - {{ $departmentName }}</div>
        <div class="sub-title">PTJ: {{ $ptjCode }}</div>
    </div>
</div>

@forelse ($printGroups as $group)
    @php
        $groupTotal = $group['total'];
        $grandTotal += $groupTotal;
    @endphp
    <div class="request-block">
        <div class="request-meta">
            <div><span class="label">Bahagian:</span> {{ $group['unit'] }}</div>
            <div><span class="label">Tahun:</span> {{ implode(', ', $group['years']) ?: '-' }}</div>
        </div>

        <table>
            <colgroup>
                <col style="width: 6%;">
                <col style="width: 8%;">
                <col style="width: 10%;">
                <col style="width: 18%;">
                <col style="width: 16%;">
                <col style="width: 8%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 24%;">
            </colgroup>
            <tr>
                <th>Bilangan</th>
                <th>Tahun</th>
                <th>Kod</th>
                <th>Jenis Aset</th>
                <th>Perincian Aset</th>
                <th>Kuantiti</th>
                <th>Harga (RM)</th>
                <th>Jumlah (RM)</th>
                <th>Ulasan</th>
            </tr>
            @foreach ($group['items'] as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['year'] }}</td>
                    <td>{{ $item['code'] }}</td>
                    <td class="text-left">{{ $item['category'] }}</td>
                    <td class="text-left">{{ $item['detail'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['price'], 2) }}</td>
                    <td>{{ number_format($item['total'], 2) }}</td>
                    <td class="text-left">{{ $item['review'] }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7">Jumlah Bahagian {{ $group['unit'] }}</td>
                <td colspan="2">RM {{ number_format($groupTotal, 2) }}</td>
            </tr>
        </table>
    </div>
@empty
    <p class="muted">Tiada rekod dijumpai untuk penapis yang dipilih.</p>
@endforelse

@if ($printGroups->isNotEmpty())
    <table>
        <tr class="total-row">
            <td colspan="7">Jumlah Keseluruhan</td>
            <td colspan="2">RM {{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>
@endif

<div class="footer">
    <div class="signature">
        <div>___________________________<br>Disediakan oleh</div>
        <div>___________________________<br>Disemak oleh</div>
        <div>___________________________<br>Diluluskan oleh</div>
    </div>
</div>

<script>window.print();</script>
</body>
</html>
