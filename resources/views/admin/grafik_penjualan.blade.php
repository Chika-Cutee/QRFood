@extends('layouts.admin')

@section('title', 'Grafik Penjualan')
@section('header_title', 'Grafik Penjualan')

@section('content')

    <div style="background-color: white; padding: 20px; border-radius: 8px;">
        
        <h3 style="text-align: center; margin-bottom: 2rem;">GRAFIK PENJUALAN PERBULAN (Tahun {{ $tahunSaatIni }})</h3>
        <div style="width: 100%; max-height: 400px; margin: 0 auto;">
            <canvas id="penjualanBulananChart"></canvas>
        </div>

        <hr style="margin: 3rem 0; border-top: 2px solid #eee;">

        <h3 style="text-align: center; margin-bottom: 2rem;">GRAFIK PENJUALAN PERTAHUN</h3>
        <div style="width: 100%; max-height: 400px; margin: 0 auto;">
            <canvas id="penjualanTahunanChart"></canvas>
        </div>
        
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script> 

<script>
    // Data dari Controller
    const dataBulanan = {!! $dataBulananJson !!}; 
    const labelsTahun = {!! $labelsTahun !!};
    const dataTahun = {!! $dataTahunJson !!};

    const monthLabels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // === CHART PENJUALAN BULANAN ===
    new Chart(document.getElementById('penjualanBulananChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Total Penjualan (IDR)',
                data: dataBulanan,
                backgroundColor: 'rgba(185, 28, 28, 0.2)', // Merah Muda
                borderColor: '#B91C1C', // Merah Solid
                borderWidth: 2,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Penjualan (Rp)'
                    },
                    // Mengubah label Y menjadi format Rupiah (sederhana)
                    callbacks: {
                        label: function(context) {
                            let value = context.parsed.y;
                            return 'Rp' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // === CHART PENJUALAN TAHUNAN ===
    new Chart(document.getElementById('penjualanTahunanChart'), {
        type: 'line',
        data: {
            labels: labelsTahun,
            datasets: [{
                label: 'Total Penjualan Tahunan (IDR)',
                data: dataTahun,
                backgroundColor: 'rgba(29, 78, 216, 0.2)', // Biru Muda
                borderColor: '#1D4ED8', // Biru Solid
                borderWidth: 2,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Penjualan (Rp)'
                    },
                    callbacks: {
                        label: function(context) {
                            let value = context.parsed.y;
                            return 'Rp' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush