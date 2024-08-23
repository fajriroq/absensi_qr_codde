<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <!-- REKAP JUMLAH DATA -->
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <a href="<?= base_url('admin/karyawan'); ?>" class="text-white">
                                <i class="material-icons">person_4</i>
                            </a>
                        </div>
                        <p class="card-category">Jumlah Karyawan</p>
                        <h3 class="card-title"><?= count($karyawan); ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-success">check</i>
                            Terdaftar
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GRAFIK CHART -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-chart">
                    <div class="card-header card-header-success">
                        <div class="ct-chart" id="kehadiranKaryawan"></div>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">Tingkat kehadiran Karyawan</h4>
                        <p class="card-category">Jumlah kehadiran Karyawan dalam 7 hari terakhir</p>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-success">checklist</i> 
                            <a class="text-success" href="<?= base_url('admin/absen-karyawan'); ?>">Lihat data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chartist JS -->
<script src="<?= base_url('assets/js/plugins/chartist.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        initDashboardPageCharts();
    });

    function initDashboardPageCharts() {
        if ($('#kehadiranKaryawan').length != 0) {
            /* ----------==========     Chart tingkat kehadiran karyawan    ==========---------- */
            const dataKehadiranKaryawan = [<?php foreach ($grafikKehadiranKaryawan as $value) echo "$value,"; ?>];

            const chartKehadiranKaryawan = {
                labels: [
                    <?php
                    foreach ($dateRange as  $value) {
                        echo "'$value',";
                    }
                    ?>
                ],
                series: [dataKehadiranKaryawan]
            };

            var highestData = 0;

            dataKehadiranKaryawan.forEach(e => {
                if (e >= highestData) {
                    highestData = e;
                }
            });

            const optionsChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: highestData + (highestData / 4),
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            };

            var kehadiranKaryawanChart = new Chartist.Line('#kehadiranKaryawan', chartKehadiranKaryawan, optionsChart);
            md.startAnimationForLineChart(kehadiranKaryawanChart);
        }
    }
</script>
<?= $this->endSection() ?>
