<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\PetugasModel;
use App\Models\PresensiKaryawanModel; // Ganti PresensiGuruModel dengan PresensiKaryawanModel
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;
use Config\AbsensiSekolah as ConfigAbsensiSekolah;

class Dashboard extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KaryawanModel $karyawanModel; // Ganti GuruModel dengan KaryawanModel

   protected KelasModel $kelasModel; // Pastikan nama variabel mengikuti konvensi penamaan

   protected PresensiSiswaModel $presensiSiswaModel;
   protected PresensiKaryawanModel $presensiKaryawanModel; // Ganti PresensiGuruModel dengan PresensiKaryawanModel

   protected PetugasModel $petugasModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->karyawanModel = new KaryawanModel(); // Ganti GuruModel dengan KaryawanModel
      $this->kelasModel = new KelasModel(); // Pastikan nama variabel mengikuti konvensi penamaan
      $this->presensiSiswaModel = new PresensiSiswaModel();
      $this->presensiKaryawanModel = new PresensiKaryawanModel(); // Ganti PresensiGuruModel dengan PresensiKaryawanModel
      $this->petugasModel = new PetugasModel();
   }

   public function index()
   {
      $now = Time::now();

      $dateRange = [];
      $siswaKehadiranArray = [];
      $karyawanKehadiranArray = []; // Ganti guruKehadiranArray dengan karyawanKehadiranArray

      for ($i = 6; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();
         if ($i == 0) {
            $formattedDate = "Hari ini";
         } else {
            $t = $now->subDays($i);
            $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
         }
         array_push($dateRange, $formattedDate);
         array_push(
            $siswaKehadiranArray,
            count($this->presensiSiswaModel
               ->join('tb_siswa', 'tb_presensi_siswa.id_siswa = tb_siswa.id_siswa', 'left')
               ->where(['tb_presensi_siswa.tanggal' => "$date", 'tb_presensi_siswa.id_kehadiran' => '1'])->findAll())
         );
         array_push(
            $karyawanKehadiranArray, // Ganti guruKehadiranArray dengan karyawanKehadiranArray
            count($this->presensiKaryawanModel // Ganti presensiGuruModel dengan presensiKaryawanModel
               ->join('tb_karyawan', 'tb_presensi_karyawan.id_karyawan = tb_karyawan.id_karyawan', 'left')
               ->where(['tb_presensi_karyawan.tanggal' => "$date", 'tb_presensi_karyawan.id_kehadiran' => '1'])->findAll())
         );
      }

      $today = $now->toDateString();

      $data = [
         'title' => 'Dashboard',
         'ctx' => 'dashboard',

         'siswa' => $this->siswaModel->getAllSiswaWithKelas(),
         'karyawan' => $this->karyawanModel->getAllKaryawan(), // Ganti guru dengan karyawan

         'kelas' => $this->kelasModel->getDataKelas(), // Pastikan nama variabel mengikuti konvensi penamaan

         'dateRange' => $dateRange,
         'dateNow' => $now->toLocalizedString('d MMMM Y'),

         'grafikKehadiranSiswa' => $siswaKehadiranArray,
         'grafikKehadiranKaryawan' => $karyawanKehadiranArray, // Ganti grafikKehadiranGuru dengan grafikKehadiranKaryawan

         'jumlahKehadiranSiswa' => [
            'hadir' => count($this->presensiSiswaModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiSiswaModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiSiswaModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiSiswaModel->getPresensiByKehadiran('4', $today))
         ],

         'jumlahKehadiranKaryawan' => [ // Ganti jumlahKehadiranGuru dengan jumlahKehadiranKaryawan
            'hadir' => count($this->presensiKaryawanModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiKaryawanModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiKaryawanModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiKaryawanModel->getPresensiByKehadiran('4', $today))
         ],

         'petugas' => $this->petugasModel->getAllPetugas(),
      ];

      return view('admin/dashboard', $data);
   }
}
