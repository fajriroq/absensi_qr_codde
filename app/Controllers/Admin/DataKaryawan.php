<?php

namespace App\Controllers\Admin;

use App\Models\KaryawanModel;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataKaryawan extends BaseController
{
   protected KaryawanModel $karyawanModel;

   protected $karyawanValidationRules = [
      'nik' => [
         'rules' => 'required|max_length[20]|min_length[16]',
         'errors' => [
            'required' => 'NIK harus diisi.',
            'is_unique' => 'NIK ini telah terdaftar.',
            'min_length[16]' => 'Panjang NIK minimal 16 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Karyawan',
         'ctx' => 'karyawan',
      ];

      return view('admin/data/data-karyawan', $data);
   }

   public function ambilDataKaryawan()
   {
      $result = $this->karyawanModel->getAllKaryawan();

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-karyawan', $data);
   }

   public function formTambahKaryawan()
   {
      $data = [
         'ctx' => 'karyawan',
         'title' => 'Tambah Data Karyawan'
      ];

      return view('admin/data/create/create-data-karyawan', $data);
   }

   public function saveKaryawan()
   {
      // validasi
      if (!$this->validate($this->karyawanValidationRules)) {
         $data = [
            'ctx' => 'karyawan',
            'title' => 'Tambah Data Karyawan',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-karyawan', $data);
      }

      // simpan
      $result = $this->karyawanModel->createKaryawan(
         nik: $this->request->getVar('nik'),
         nama: $this->request->getVar('nama'),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/karyawan');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/karyawan/create/');
   }

   public function formEditKaryawan($id)
   {
      $karyawan = $this->karyawanModel->getKaryawanById($id);

      if (empty($karyawan)) {
         throw new PageNotFoundException('Data karyawan dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $karyawan,
         'ctx' => 'karyawan',
         'title' => 'Edit Data Karyawan',
      ];

      return view('admin/data/edit/edit-data-karyawan', $data);
   }

   public function updateKaryawan()
   {
      $idKaryawan = $this->request->getVar('id');

      // validasi
      if (!$this->validate($this->karyawanValidationRules)) {
         $data = [
            'data' => $this->karyawanModel->getKaryawanById($idKaryawan),
            'ctx' => 'karyawan',
            'title' => 'Edit Data Karyawan',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-karyawan', $data);
      }

      // update
      $result = $this->karyawanModel->updateKaryawan(
         id: $idKaryawan,
         nik: $this->request->getVar('nik'),
         nama: $this->request->getVar('nama'),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/karyawan');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/karyawan/edit/' . $idkaryawan);
   }

   public function delete($id)
   {
      $result = $this->karyawanModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/karyawan');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/karyawan');
   }
}