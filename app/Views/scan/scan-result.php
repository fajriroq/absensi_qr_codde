<?php

use App\Libraries\enums\TipeUser;

switch ($type) {
   case TipeUser::Siswa:
?>
      <h3 class="text-success">Absen <?= $waktu; ?> berhasil</h3>
      <div class="row w-100">
         <div class="col">
            <p>Nama : <b><?= $data['nama_siswa']; ?></b></p>
            <p>NIS : <b><?= $data['nis']; ?></b></p>
            <p>Kelas : <b><?= $data['kelas']  . ' ' . $data['jurusan']; ?></b></p>
         </div>
         <div class="col">
            <?= jam($presensi); ?>
         </div>
      </div>
   <?php break;

   case TipeUser::Karyawan:
   ?>
      <h3 class="text-success">Absen <?= $waktu; ?> berhasil</h3>
      <div class="row w-100">
         <div class="col">
            <p>Nama : <b><?= $data['nama_karyawan']; ?></b></p>
            <p>NIK : <b><?= $data['nik']; ?></b></p>
            <p>No HP : <b><?= $data['no_hp']; ?></b></p>
         </div>
         <div class="col">
            <?= jam($presensi); ?>
         </div>
      </div>
   <?php break;

   default:
   ?>
      <h3 class="text-danger">Tipe tidak valid</h3>
   <?php
      break;
}

function jam($presensi)
{
   ?>
   <p>Jam masuk : <b class="text-info"><?= $presensi['jam_masuk'] ?? '-'; ?></b></p>
   <p>Jam pulang : <b class="text-info"><?= $presensi['jam_keluar'] ?? '-'; ?></b></p>
<?php
}

?>