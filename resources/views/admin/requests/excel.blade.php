<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Permohonan Aset</title>
</head>
<body>
@php
    $grandTotal = 0;
@endphp

<table border="1">
    <tr>
        <th colspan="9">Butiran Anggaran Perbelanjaan dan Ulasan</th>
    </tr>
    <tr>
        <th colspan="9">Kod {{ $assetMainCategory['code'] }} - {{ $assetMainCategory['label'] }}</th>
    </tr>
    <tr>
        <th colspan="9">Jabatan: {{ $departmentCode }} - {{ $departmentName }}</th>
    </tr>
    <tr>
        <th colspan="9">PTJ: {{ $ptjCode }}</th>
    </tr>
</table>

<table border="1">
    <tr>
        <th>Bilangan</th>
        <th>Tahun</th>
        <th>Bahagian</th>
        <th>Kod</th>
        <th>Jenis Aset</th>
        <th>Perincian Aset</th>
        <th>Kuantiti</th>
        <th>Harga (RM)</th>
        <th>Jumlah (RM)</th>
        <th>Ulasan</th>
    </tr>
    @forelse ($printGroups as $group)
        @php
            $groupTotal = $group['total'];
            $grandTotal += $groupTotal;
        @endphp
        @foreach ($group['items'] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['year'] }}</td>
                <td>{{ $group['unit'] }}</td>
                <td>{{ $item['code'] }}</td>
                <td>{{ $item['category'] }}</td>
                <td>{{ $item['detail'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'], 2, '.', '') }}</td>
                <td>{{ number_format($item['total'], 2, '.', '') }}</td>
                <td>{{ $item['review'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="8"><strong>Jumlah Bahagian {{ $group['unit'] }}</strong></td>
            <td><strong>{{ number_format($groupTotal, 2, '.', '') }}</strong></td>
            <td></td>
        </tr>
    @empty
        <tr>
            <td colspan="10">Tiada rekod dijumpai untuk penapis yang dipilih.</td>
        </tr>
    @endforelse
    @if ($printGroups->isNotEmpty())
        <tr>
            <td colspan="8"><strong>Jumlah Keseluruhan</strong></td>
            <td><strong>{{ number_format($grandTotal, 2, '.', '') }}</strong></td>
            <td></td>
        </tr>
    @endif
</table>
</body>
</html>
