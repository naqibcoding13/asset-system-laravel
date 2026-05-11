@extends('layouts.app')

@section('content')

<style>
body {
    position: relative;
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background-image: url("{{ asset('images/background.jpeg') }}");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    opacity: 0.3;
}

.chart-caption {
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.chart-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #dbe5f0;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
}

.chart-chip-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

</style>

<div class="hero-strip">
    <h2 class="fw-bold mb-1">Analisis Permohonan Aset</h2>
    <p class="mb-0 opacity-75">Visualisasi data statistik permohonan tahunan dan kategori aset.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="page-card p-4">
            <div class="fw-bold text-uppercase mb-3" style="font-size: 0.9rem; color: #1e3a8a;">
                <i class="bi bi-calendar3 text-danger me-2"></i>Tren Permohonan Mengikut Tahun
            </div>
            <div style="height: 300px;">
                <canvas id="yearChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="page-card p-4">
            <div class="fw-bold text-uppercase mb-3" style="font-size: 0.9rem; color: #1e3a8a;">
                <i class="bi bi-bar-chart-steps text-success me-2"></i>Perbandingan Aset Diluluskan dan Ditolak Mengikut Tahun
            </div>
            <div style="height: 320px;">
                <canvas id="statusByYearChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="page-card p-4 h-100">
            <div class="fw-bold text-uppercase mb-3" style="font-size: 0.9rem; color: #1e3a8a;">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Pecahan Jenis Aset Dimohon
            </div>
            <div style="height: 350px;">
                <canvas id="assetChart"></canvas>
            </div>
            <div class="chart-caption" id="assetChartCaption"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="page-card p-4 h-100">
            <div class="fw-bold text-uppercase mb-3" style="font-size: 0.9rem; color: #1e3a8a;">
                <i class="bi bi-x-circle-fill text-danger me-2"></i>Statistik Aset Ditolak (Ditolak)
            </div>
            <div style="height: 350px;">
                <canvas id="rejectChart"></canvas>
            </div>
            <div class="small text-muted mt-3">
                Paparan sisi menggunakan kod aset ringkas. Perincian penuh boleh dilihat pada tooltip graf.
            </div>
        </div>
    </div>
</div>

<div class="page-card p-4">
    <h4 class="fw-bold mb-3">Rumusan Permohonan dan Bajet</h4>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tahun</th>
                    <th>Bahagian</th>
                    <th>Jumlah Permohonan</th>
                    <th>Anggaran Bajet</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summaries as $summary)
                    <tr>
                        <td>{{ $summary->tahun }}</td>
                        <td>{{ $summary->unit }}</td>
                        <td>{{ $summary->total_requests }}</td>
                        <td>RM {{ number_format($summary->total_budget, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada data untuk dilaporkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const colorBlue = '#1e3a8a';
    const colorRed = '#a30000';
    const chartPalette = ['#1e3a8a', '#a30000', '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#6366f1', '#0f766e', '#92400e'];

    // ✅ SAFEST METHOD (no JS parsing error)
    const yearLabels = JSON.parse('@json($yearLabels)');
    const yearTotals = JSON.parse('@json($yearTotals)');
    const yearPercentages = JSON.parse('@json($yearPercentages)');

    const assetLabels = JSON.parse('@json($assetLabels)');
    const assetCodes = JSON.parse('@json($assetCodes)');
    const assetTotals = JSON.parse('@json($assetTotals)');
    const assetPercentages = JSON.parse('@json($assetPercentages)');

    const rejectLabels = JSON.parse('@json($rejectLabels)');
    const rejectCodes = JSON.parse('@json($rejectCodes)');
    const rejectTotals = JSON.parse('@json($rejectTotals)');
    const rejectPercentages = JSON.parse('@json($rejectPercentages)');
    const approvedByYear = JSON.parse('@json($approvedByYear)');
    const rejectedByYear = JSON.parse('@json($rejectedByYear)');
    const approvedPercentages = JSON.parse('@json($approvedPercentages)');
    const rejectedPercentages = JSON.parse('@json($rejectedPercentages)');

    const percentFormatter = value => `${Number(value).toFixed(1)}%`;
    const integerTickOptions = {
        beginAtZero: true,
        ticks: {
            precision: 0,
            stepSize: 1,
            callback: value => Number.isInteger(value) ? value : ''
        }
    };
    const percentageTickOptions = {
        beginAtZero: true,
        max: 100,
        ticks: {
            callback: value => `${value}%`
        }
    };
    const statusPercentagePlugin = {
        id: 'statusPercentagePlugin',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            ctx.save();
            ctx.font = '600 11px Segoe UI';
            ctx.fillStyle = '#334155';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';

            chart.data.datasets.forEach((dataset, datasetIndex) => {
                const meta = chart.getDatasetMeta(datasetIndex);
                const percentages = dataset.percentages ?? [];

                meta.data.forEach((bar, index) => {
                    const percentage = percentages[index];
                    if (percentage === undefined || percentage === null) {
                        return;
                    }

                    ctx.fillText(percentFormatter(percentage), bar.x, bar.y - 6);
                });
            });

            ctx.restore();
        }
    };
    const horizontalPercentagePlugin = {
        id: 'horizontalPercentagePlugin',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            ctx.save();
            ctx.font = '600 11px Segoe UI';
            ctx.fillStyle = '#334155';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';

            chart.data.datasets.forEach((dataset, datasetIndex) => {
                const meta = chart.getDatasetMeta(datasetIndex);
                const percentages = dataset.percentages ?? [];

                meta.data.forEach((bar, index) => {
                    const percentage = percentages[index];
                    if (percentage === undefined || percentage === null) {
                        return;
                    }

                    ctx.fillText(percentFormatter(percentage), bar.x + 8, bar.y);
                });
            });

            ctx.restore();
        }
    };

    const renderCodeLegend = (targetId, codes, colors) => {
        const target = document.getElementById(targetId);
        if (!target) return;

        target.innerHTML = codes.map((code, index) => `
            <span class="chart-chip">
                <span class="chart-chip-dot" style="background:${colors[index % colors.length]}"></span>
                ${code}
            </span>
        `).join('');
    };

    new Chart(document.getElementById('yearChart'), {
        type: 'bar',
        data: {
            labels: yearLabels,
            datasets: [{
                label: 'Peratus Permohonan',
                data: yearPercentages,
                counts: yearTotals,
                backgroundColor: colorBlue,
                borderRadius: 5
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const percentage = context.raw ?? 0;
                            const total = context.dataset.counts?.[context.dataIndex] ?? 0;

                            return `${percentFormatter(percentage)} (${total} permohonan)`;
                        }
                    }
                }
            },
            scales: { y: percentageTickOptions }
        }
    });

    new Chart(document.getElementById('statusByYearChart'), {
        type: 'bar',
        data: {
            labels: yearLabels,
            datasets: [
                {
                    label: 'Diluluskan',
                    data: approvedByYear,
                    percentages: approvedPercentages,
                    backgroundColor: '#10b981',
                    borderRadius: 5
                },
                {
                    label: 'Ditolak',
                    data: rejectedByYear,
                    percentages: rejectedPercentages,
                    backgroundColor: colorRed,
                    borderRadius: 5
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label(context) {
                            const count = context.raw ?? 0;
                            const percentage = context.dataset.percentages?.[context.dataIndex] ?? 0;

                            return `${context.dataset.label}: ${count} (${percentFormatter(percentage)})`;
                        }
                    }
                }
            },
            scales: {
                y: integerTickOptions
            }
        },
        plugins: [statusPercentagePlugin]
    });

    new Chart(document.getElementById('assetChart'), {
        type: 'doughnut',
        data: {
            labels: assetLabels,
            datasets: [{
                data: assetTotals,
                backgroundColor: chartPalette,
                hoverOffset: 10
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const value = context.raw ?? 0;
                            const percentage = assetPercentages[context.dataIndex] ?? 0;

                            return `${context.label || 'Tidak dinyatakan'}: ${percentFormatter(percentage)} (${value})`;
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('rejectChart'), {
        type: 'bar',
        data: {
            labels: rejectCodes,
            datasets: [{
                label: 'Peratus Ditolak',
                data: rejectPercentages,
                counts: rejectTotals,
                fullLabels: rejectLabels,
                percentages: rejectPercentages,
                backgroundColor: colorRed,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title(items) {
                            const index = items[0]?.dataIndex ?? 0;
                            return rejectLabels[index] ?? rejectCodes[index];
                        },
                        label(context) {
                            const percentage = context.dataset.percentages?.[context.dataIndex] ?? 0;
                            const total = context.dataset.counts?.[context.dataIndex] ?? 0;

                            return `${percentFormatter(percentage)} (${total} item ditolak)`;
                        }
                    }
                }
            },
            scales: { x: percentageTickOptions }
        },
        plugins: [horizontalPercentagePlugin]
    });

    renderCodeLegend('assetChartCaption', assetCodes, chartPalette);
</script>
@endpush
