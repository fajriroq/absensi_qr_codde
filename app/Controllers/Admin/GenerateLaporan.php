<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use DateTime;
use DateInterval;
use DatePeriod;

use App\Models\KaryawanModel;
use App\Models\KelasModel;
use App\Models\PresensiKaryawanModel; // Update model presensi guru ke karyawan
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;

class GenerateLaporan extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;

   protected KaryawanModel $karyawanModel; // Update guruModel menjadi karyawanModel

   protected PresensiSiswaModel $presensiSiswaModel;
   protected PresensiKaryawanModel $presensiKaryawanModel; // Update presensiGuruModel menjadi presensiKaryawanModel

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();

      $this->karyawanModel = new KaryawanModel(); // Update guruModel menjadi karyawanModel

      $this->presensiSiswaModel = new PresensiSiswaModel();
      $this->presensiKaryawanModel = new PresensiKaryawanModel(); // Update presensiGuruModel menjadi presensiKaryawanModel
   }

   public function index()
   {
      $kelas = $this->kelasModel->getDataKelas();
      $karyawan = $this->karyawanModel->getAllKaryawan(); // Update getAllGuru menjadi getAllKaryawan

      $siswaPerKelas = [];

      foreach ($kelas as $value) {
         array_push($siswaPerKelas, $this->siswaModel->getSiswaByKelas($value['id_kelas']));
      }

      $data = [
         'title' => 'Generate Laporan',
         'ctx' => 'laporan',
         'siswaPerKelas' => $siswaPerKelas,
         'kelas' => $kelas,
         'karyawan' => $karyawan // Update guru menjadi karyawan
      ];

      return view('admin/generate-laporan/generate-laporan', $data);
   }

   public function generateLaporanSiswa()
   {
      $idKelas = $this->request->getVar('kelas');
      $siswa = $this->siswaModel->getSiswaByKelas($idKelas);
      $type = $this->request->getVar('type');

      if (empty($siswa)) {
         session()->setFlashdata([
            'msg' => 'Data siswa kosong!',
            'error' => true
         ]);
         return redirect()->to('/admin/laporan');
      }

      $kelas = $this->kelasModel->where(['id_kelas' => $idKelas])
         ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
         ->first();

      $bulan = $this->request->getVar('tanggalSiswa');

      // hari pertama dalam 1 bulan
      $begin = new Time($bulan, locale: 'id');
      // tanggal terakhir dalam 1 bulan
      $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
      // interval 1 hari
      $interval = DateInterval::createFromDateString('1 day');
      // buat array dari semua hari di bulan
      $period = new DatePeriod($begin, $interval, $end);

      $arrayTanggal = [];
      $dataAbsen = [];

      foreach ($period as $value) {
         // kecualikan hari sabtu dan minggu
         if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
            $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());

            $absenByTanggal = $this->presensiSiswaModel
               ->getPresensiByKelasTanggal($idKelas, $value->format('Y-m-d'));

            $absenByTanggal['lewat'] = $lewat;

            array_push($dataAbsen, $absenByTanggal);
            array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
         }
      }

      $laki = 0;

      foreach ($siswa as $value) {
         if ($value['jenis_kelamin'] != 'Perempuan') {
            $laki++;
         }
      }

      $data = [
         'tanggal' => $arrayTanggal,
         'bulan' => $begin->toLocalizedString('MMMM'),
         'listAbsen' => $dataAbsen,
         'listSiswa' => $siswa,
         'jumlahSiswa' => [
            'laki' => $laki,
            'perempuan' => count($siswa) - $laki
         ],
         'kelas' => $kelas,
         'grup' => "kelas " . $kelas['kelas'] . " " . $kelas['jurusan'],
      ];

      if ($type == 'doc') {
         $this->response->setHeader('Content-type', 'application/vnd.ms-word');
         $this->response->setHeader(
            'Content-Disposition',
            'attachment;Filename=laporan_absen_' . $kelas['kelas'] . " " . $kelas['jurusan'] . '_' . $begin->toLocalizedString('MMMM-Y') . '.doc'
         );

         return view('admin/generate-laporan/laporan-siswa', $data);
      }

      return view('admin/generate-laporan/laporan-siswa', $data) . view('admin/generate-laporan/topdf');
   }

   public function generateLaporanKaryawan() // Update generateLaporanGuru menjadi generateLaporanKaryawan
   {
      $karyawan = $this->karyawanModel->getAllKaryawan(); // Update getAllGuru menjadi getAllKaryawan
      $type = $this->request->getVar('type');

      if (empty($karyawan)) {
         session()->setFlashdata([
            'msg' => 'Data karyawan kosong!', // Update pesan
            'error' => true
         ]);
         return redirect()->to('/admin/laporan');
      }

      $bulan = $this->request->getVar('tanggalKaryawan'); // Update tanggalGuru menjadi tanggalKaryawan

      // hari pertama dalam 1 bulan
      $begin = new Time($bulan, locale: 'id');
      // tanggal terakhir dalam 1 bulan
      $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
      // interval 1 hari
      $interval = DateInterval::createFromDateString('1 day');
      // buat array dari semua hari di bulan
      $period = new DatePeriod($begin, $interval, $end);

      $arrayTanggal = [];
      $dataAbsen = [];

      foreach ($period as $value) {
         // kecualikan hari sabtu dan minggu
         if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
            $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());

            $absenByTanggal = $this->presensiKaryawanModel
               ->getPresensiByTanggal($value->format('Y-m-d'));

            $absenByTanggal['lewat'] = $lewat;

            array_push($dataAbsen, $absenByTanggal);
            array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
         }
      }

      $laki = 0;

      foreach ($karyawan as $value) {
         if ($value['jenis_kelamin'] != 'Perempuan') {
            $laki++;
         }
      }

      $data = [
         'tanggal' => $arrayTanggal,
         'bulan' => $begin->toLocalizedString('MMMM'),
         'listAbsen' => $dataAbsen,
         'listKaryawan' => $karyawan, // Update listGuru menjadi listKaryawan
         'jumlahKaryawan' => [ // Update jumlahGuru menjadi jumlahKaryawan
            'laki' => $laki,
            'perempuan' => count($karyawan) - $laki
         ],
         'grup' => 'karyawan',
      ];

      if ($type == 'doc') {
         $this->response->setHeader('Content-type', 'application/vnd.ms-word');
         $this->response->setHeader(
            'Content-Disposition',
            'attachment;Filename=laporan_absen_karyawan_' . $begin->toLocalizedString('MMMM-Y') . '.doc' // Update laporan_absen_guru menjadi laporan_absen_karyawan
         );

         return view('admin/generate-laporan/laporan-karyawan', $data); // Update view
      }

      return view('admin/generate-laporan/laporan-karyawan', $data) . view('admin/generate-laporan/topdf'); // Update view
   }
}
