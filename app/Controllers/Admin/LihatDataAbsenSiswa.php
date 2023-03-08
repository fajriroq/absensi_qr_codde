<?php

namespace App\Controllers\Admin;

use App\Models\KelasModel;

use App\Models\SiswaModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiSiswaModel;
use CodeIgniter\I18n\Time;

class LihatDataAbsenSiswa extends BaseController
{
   protected KelasModel $kelasModel;

   protected SiswaModel $siswaModel;

   protected KehadiranModel $kehadiranModel;

   protected PresensiSiswaModel $presensiSiswa;

   protected string $currentDate;

   public function __construct()
   {
      $this->currentDate = Time::today()->toDateString();

      $this->siswaModel = new SiswaModel();

      $this->kehadiranModel = new KehadiranModel();

      $this->kelasModel = new KelasModel();

      $this->presensiSiswa = new PresensiSiswaModel();
   }

   public function index()
   {
      $result = $this->kelasModel->getAllKelas();

      $data = [
         'title' => 'Data Absen Siswa',
         'ctx' => 'absen-siswa',
         'data' => $result
      ];

      return view('admin/absen/absen-siswa', $data);
   }

   public function ambilDataSiswa()
   {
      // ambil variabel POST
      $kelas = $this->request->getVar('kelas');
      $idKelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiSiswa->getPresensiByKelasTanggal($idKelas, $tanggal);

      $data = [
         'kelas' => $kelas,
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-siswa', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idSiswa = $this->request->getVar('id_siswa');
      $idKelas = $this->request->getVar('id_kelas');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiSiswa->cekAbsen($idSiswa, $tanggal);

      $result = $this->presensiSiswa->updatePresensi(
         $cek == false ? NULL : $cek,
         $idSiswa,
         $idKelas,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $keterangan
      );

      $response['nama_siswa'] = $this->siswaModel->getSiswaById($idSiswa)['nama_siswa'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
