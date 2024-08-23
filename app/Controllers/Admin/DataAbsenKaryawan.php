<?php

namespace App\Controllers\Admin;

use App\Models\KaryawanModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiKaryawanModel;
use CodeIgniter\I18n\Time;

class DataAbsenKaryawan extends BaseController
{
   protected KaryawanModel $karyawanModel;

   protected PresensiKaryawanModel $presensiKaryawan;

   protected KehadiranModel $kehadiranModel;

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();

      $this->presensiKaryawan = new PresensiKaryawanModel();

      $this->kehadiranModel = new KehadiranModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Absen Karyawan',
         'ctx' => 'absen-karyawan',
      ];

      return view('admin/absen/absen-karyawan', $data);
   }

   public function ambilDataKaryawan()
   {
      // ambil variabel POST
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiKaryawan->getPresensiByTanggal($tanggal);

      $data = [
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-absen-karyawan', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idKaryawan = $this->request->getVar('id_karyawan');

      $data = [
         'presensi' => $this->presensiKaryawan->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->karyawanModel->getKaryawanById($idKaryawan)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idKaryawan = $this->request->getVar('id_karyawan');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiKaryawan->cekAbsen($idKaryawan, $tanggal);

      $result = $this->presensiKaryawan->updatePresensi(
         $cek == false ? NULL : $cek,
         $idKaryawan,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $jamKeluar ?? NULL,
         $keterangan
      );

      $response['nama_karyawan'] = $this->karyawanModel->getKaryawanById($idKaryawan)['nama_karyawan'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}