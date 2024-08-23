<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\KaryawanModel;
use App\Models\SiswaModel;
use App\Models\PresensiKaryawanModel;
use App\Models\PresensiSiswaModel;
use App\Libraries\enums\TipeUser;

class Scan extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KaryawanModel $karyawanModel;

   protected PresensiSiswaModel $presensiSiswaModel;
   protected PresensiKaryawanModel $presensiKaryawanModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->karyawanModel = new KaryawanModel();
      $this->presensiSiswaModel = new PresensiSiswaModel();
      $this->presensiKaryawanModel = new PresensiKaryawanModel();
   }

   public function index($t = 'Masuk')
   {
      $data = ['waktu' => $t, 'title' => 'Absensi Karyawan Berbasis QR Code'];
      return view('scan/scan', $data);
   }

   public function cekKode()
   {
      // ambil variabel POST
      $uniqueCode = $this->request->getVar('unique_code');
      $waktuAbsen = $this->request->getVar('waktu');

      $status = false;
      $type = TipeUser::Siswa;

      // cek data siswa di database
      $result = $this->siswaModel->cekSiswa($uniqueCode);

      if (empty($result)) {
         // jika cek siswa gagal, cek data karyawan
         $result = $this->karyawanModel->cekKaryawan($uniqueCode);

         if (!empty($result)) {
            $status = true;

            $type = TipeUser::Karyawan;
         } else {
            $status = false;

            $result = NULL;
         }
      } else {
         $status = true;
      }

      if (!$status) { // data tidak ditemukan
         return $this->showErrorView('Data tidak ditemukan');
      }

      // jika data ditemukan
      switch ($waktuAbsen) {
         case 'masuk':
            return $this->absenMasuk($type, $result);
            break;

         case 'pulang':
            return $this->absenPulang($type, $result);
            break;

         default:
            return $this->showErrorView('Data tidak valid');
            break;
      }
   }

   public function absenMasuk($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'masuk';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      // absen masuk
      switch ($type) {
         case TipeUser::Karyawan:
            $idKaryawan =  $result['id_karyawan'];
            $data['type'] = TipeUser::Karyawan;

            $sudahAbsen = $this->presensiKaryawanModel->cekAbsen($idKaryawan, $date);

            if ($sudahAbsen) {
               $data['presensi'] = $this->presensiKaryawanModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiKaryawanModel->absenMasuk($idKaryawan, $date, $time);

            $data['presensi'] = $this->presensiKaryawanModel->getPresensiByIdKaryawanTanggal($idKaryawan, $date);

            return view('scan/scan-result', $data);

         case TipeUser::Siswa:
            $idSiswa =  $result['id_siswa'];
            $idKelas =  $result['id_kelas'];
            $data['type'] = TipeUser::Siswa;

            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, Time::today()->toDateString());

            if ($sudahAbsen) {
               $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiSiswaModel->absenMasuk($idSiswa, $date, $time, $idKelas);

            $data['presensi'] = $this->presensiSiswaModel->getPresensiByIdSiswaTanggal($idSiswa, $date);

            return view('scan/scan-result', $data);

         default:
            return $this->showErrorView('Tipe tidak valid');
      }
   }

   public function absenPulang($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'pulang';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      // absen pulang
      switch ($type) {
         case TipeUser::Karyawan:
            $idKaryawan =  $result['id_karyawan'];
            $data['type'] = TipeUser::Karyawan;

            $sudahAbsen = $this->presensiKaryawanModel->cekAbsen($idKaryawan, $date);

            if (!$sudahAbsen) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiKaryawanModel->absenKeluar($sudahAbsen, $time);

            $data['presensi'] = $this->presensiKaryawanModel->getPresensiById($sudahAbsen);

            return view('scan/scan-result', $data);

         case TipeUser::Siswa:
            $idSiswa =  $result['id_siswa'];
            $data['type'] = TipeUser::Siswa;

            $sudahAbsen = $this->presensiSiswaModel->cekAbsen($idSiswa, $date);

            if (!$sudahAbsen) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiSiswaModel->absenKeluar($sudahAbsen, $time);

            $data['presensi'] = $this->presensiSiswaModel->getPresensiById($sudahAbsen);

            return view('scan/scan-result', $data);
         default:
            return $this->showErrorView('Tipe tidak valid');
      }
   }

   public function showErrorView(string $msg = 'no error message', $data = NULL)
   {
      $errdata = $data ?? [];
      $errdata['msg'] = $msg;

      return view('scan/error-scan-result', $errdata);
   }
}