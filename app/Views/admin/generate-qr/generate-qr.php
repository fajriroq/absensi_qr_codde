<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
  .progress-karyawan {
    height: 5px;
    border-radius: 0px;
    background-color: rgb(58, 192, 85);
  }

  .my-progress-bar {
    height: 5px;
    border-radius: 0px;
  }
</style>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <?php if (session()->getFlashdata('msg')) : ?>
          <div class="pb-2 px-3">
            <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success'  ?> ">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
              </button>
              <?= session()->getFlashdata('msg') ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="card">
          <div class="card-header card-header-danger">
            <h4 class="card-title"><b>Generate QR Code</b></h4>
            <p class="card-category">Generate QR berdasarkan kode unik data karyawan</p>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h4 class="text-success"><b>Data Karyawan</b></h4>
                    <p>Total jumlah karyawan : <b><?= count($karyawan); ?></b>
                      <br>
                      <a href="<?= base_url('admin/karyawan'); ?>" class="text-success">Lihat data</a>
                    </p>
                    <div class="row px-2">
                      <div class="col-12 col-xl-6 px-1">
                        <button onclick="generateAllQrKaryawan()" class="btn btn-success p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">qr_code</i>
                            </div>
                            <div>
                              <h4 class="d-inline font-weight-bold">Generate All</h4>
                              <div>
                                <div id="progressKaryawan" class="d-none mt-2">
                                  <span id="progressTextKaryawan"></span>
                                  <i id="progressSelesaiKaryawan" class="material-icons d-none" class="d-none">check</i>
                                  <div class="progress progress-karyawan">
                                    <div id="progressBarKaryawan" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </button>
                      </div>
                      <div class="col-12 col-xl-6 px-1">
                        <a href="<?= base_url('admin/qr/karyawan/download'); ?>" class="btn btn-success p-2 px-md-4 w-100">
                          <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                            <div>
                              <i class="material-icons" style="font-size: 24px;">cloud_download</i>
                            </div>
                            <div>
                              <div class="text-start">
                                <h4 class="d-inline font-weight-bold">Download All</h4>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                    <br>
                    <br>
                    <p>
                      Untuk generate/download QR Code per masing-masing karyawan kunjungi
                      <a href="<?= base_url('admin/karyawan'); ?>" class="text-success"><b>data karyawan</b></a>
                    </p>
                  </div>
                </div>
                <p class="text-danger">
                  <i class="material-icons" style="font-size: 16px;">warning</i>
                  File image QR Code tersimpan di [folder website]/public/uploads/
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  const dataKaryawan = [
    <?php foreach ($karyawan as $value) {
      echo "{
              'nama' : `$value[nama_karyawan]`,
              'unique_code' : `$value[unique_code]`,
              'nomor' : `$value[nik]`
            },";
    }; ?>
  ];

  function generateAllQrKaryawan() {
    var i = 1;
    $('#progressKaryawan').removeClass('d-none');
    $('#progressBarKaryawan')
      .attr('aria-valuenow', '0')
      .attr('aria-valuemin', '0')
      .attr('aria-valuemax', dataKaryawan.length)
      .attr('style', 'width: 0%;');

    dataKaryawan.forEach(element => {
      jQuery.ajax({
        url: "<?= base_url('admin/generate/karyawan'); ?>",
        type: 'post',
        data: {
          nama: element['nama'],
          unique_code: element['unique_code'],
          nomor: element['nomor']
        },
        success: function(response) {
          if (!response) return;
          if (i != dataKaryawan.length) {
            $('#progressTextKaryawan').html('Progres: ' + i + '/' + dataKaryawan.length);
          } else {
            $('#progressTextKaryawan').html('Progres: ' + i + '/' + dataKaryawan.length + ' selesai');
            $('#progressSelesaiKaryawan').removeClass('d-none');
          }

          $('#progressBarKaryawan')
            .attr('aria-valuenow', i)
            .attr('style', 'width: ' + (i / dataKaryawan.length) * 100 + '%;');
          i++;
        }
      });
    });
  }
</script>
<?= $this->endSection() ?>
