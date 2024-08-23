<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
   protected $allowedFields = [
      'nik',
      'nama_karyawan',
      'jenis_kelamin',
      'alamat',
      'no_hp',
      'unique_code'
   ];

   protected $table = 'tb_karyawan';

   protected $primaryKey = 'id_karyawan';

   public function cekKaryawan(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getAllKaryawan()
   {
      return $this->orderBy('nama_karyawan')->findAll();
   }

   public function getKaryawanById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function createKaryawan($nik, $nama, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         'nik' => $nik,
         'nama_karyawan' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'unique_code' => sha1($nama . md5($nik . $nama . $noHp)) . substr(sha1($nik . rand(0, 100)), 0, 24)
      ]);
   }

   public function updateKaryawan($id, $nik, $nama, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nik' => $nik,
         'nama_karyawan' => $nama,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
      ]);
   }
}