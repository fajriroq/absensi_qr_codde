<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Scan
$routes->get('/', 'Scan::index');

$routes->group('scan', function (RouteCollection $routes) {
   $routes->get('', 'Scan::index');
   $routes->get('masuk', 'Scan::index/Masuk');
   $routes->get('pulang', 'Scan::index/Pulang');

   $routes->post('cek', 'Scan::cekKode');
});



// Admin
$routes->group('admin', function (RouteCollection $routes) {
   // Admin dashboard
   $routes->get('', 'Admin\Dashboard::index');
   $routes->get('dashboard', 'Admin\Dashboard::index');

   // Kelas
   $routes->group('kelas', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'KelasController::index');
      $routes->get('tambah', 'KelasController::tambahKelas');
      $routes->post('tambahKelasPost', 'KelasController::tambahKelasPost');
      $routes->get('edit/(:any)', 'KelasController::editKelas/$1');
      $routes->post('editKelasPost', 'KelasController::editKelasPost');
      $routes->post('deleteKelasPost', 'KelasController::deleteKelasPost');
      $routes->post('list-data', 'KelasController::listData');
   });

   // Jurusan
   $routes->group('jurusan', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'JurusanController::index');
      $routes->get('tambah', 'JurusanController::tambahJurusan');
      $routes->post('tambahJurusanPost', 'JurusanController::tambahJurusanPost');
      $routes->get('edit/(:any)', 'JurusanController::editJurusan/$1');
      $routes->post('editJurusanPost', 'JurusanController::editJurusanPost');
      $routes->post('deleteJurusanPost', 'JurusanController::deleteJurusanPost');
      $routes->post('list-data', 'JurusanController::listData');
   });

   // admin lihat data siswa
   $routes->get('siswa', 'Admin\DataSiswa::index');
   $routes->post('siswa', 'Admin\DataSiswa::ambilDataSiswa');
   // admin tambah data siswa
   $routes->get('siswa/create', 'Admin\DataSiswa::formTambahSiswa');
   $routes->post('siswa/create', 'Admin\DataSiswa::saveSiswa');
   // admin edit data siswa
   $routes->get('siswa/edit/(:any)', 'Admin\DataSiswa::formEditSiswa/$1');
   $routes->post('siswa/edit', 'Admin\DataSiswa::updateSiswa');
   // admin hapus data siswa
   $routes->delete('siswa/delete/(:any)', 'Admin\DataSiswa::delete/$1');
   $routes->get('siswa/bulk', 'Admin\DataSiswa::bulkPostSiswa');

   // POST Data Siswa

   $routes->group('siswa', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->post('downloadCSVFilePost', 'DataSiswa::downloadCSVFilePost');
      $routes->post('generateCSVObjectPost', 'DataSiswa::generateCSVObjectPost');
      $routes->post('importCSVItemPost', 'DataSiswa::importCSVItemPost');
      $routes->post('deleteSelectedSiswa', 'DataSiswa::deleteSelectedSiswa');
   });


   $routes->get('karyawan', 'Admin\DataKaryawan::index');
   $routes->post('karyawan', 'Admin\DataKaryawan::ambilDataKaryawan');
   $routes->get('karyawan/create', 'Admin\DataKaryawan::formTambahKaryawan');
   $routes->post('karyawan/create', 'Admin\DataKaryawan::saveKaryawan');
   $routes->get('karyawan/edit/(:num)', 'Admin\DataKaryawan::formEditKaryawan/$1');
   $routes->post('karyawan/edit', 'Admin\DataKaryawan::updateKaryawan');
   $routes->delete('karyawan/delete/(:num)', 'Admin\DataKaryawan::delete/$1');
   
   
 


   // admin lihat data absen siswa
   $routes->get('absen-siswa', 'Admin\DataAbsenSiswa::index');
   $routes->post('absen-siswa', 'Admin\DataAbsenSiswa::ambilDataSiswa'); // ambil siswa berdasarkan kelas dan tanggal
   $routes->post('absen-siswa/kehadiran', 'Admin\DataAbsenSiswa::ambilKehadiran'); // ambil kehadiran siswa
   $routes->post('absen-siswa/edit', 'Admin\DataAbsenSiswa::ubahKehadiran'); // ubah kehadiran siswa

   // admin lihat data absen karyawan
   $routes->get('absen-karyawan', 'Admin\DataAbsenKaryawan::index');
   $routes->post('absen-karyawan', 'Admin\DataAbsenKaryawan::ambilDataKaryawan'); // ambil karyawan berdasarkan tanggal
   $routes->post('absen-karyawan/kehadiran', 'Admin\DataAbsenKaryawan::ambilKehadiran'); // ambil kehadiran karyawan
   $routes->post('absen-karyawan/edit', 'Admin\DataAbsenKaryawan::ubahKehadiran'); // ubah kehadiran karyawan
   

   // admin generate QR
   $routes->get('generate', 'Admin\GenerateQR::index');
   $routes->post('generate/siswa-by-kelas', 'Admin\GenerateQR::getSiswaByKelas'); // ambil siswa berdasarkan kelas

   // Generate QR
   $routes->post('generate/siswa', 'Admin\QRGenerator::generateQrSiswa');
   $routes->post('generate/karyawan', 'Admin\QRGenerator::generateQrKaryawan');

   // Download QR
   $routes->get('qr/siswa/download', 'Admin\QRGenerator::downloadAllQrSiswa');
   $routes->get('qr/siswa/(:any)/download', 'Admin\QRGenerator::downloadQrSiswa/$1');
   $routes->get('qr/karyawan/download', 'Admin\QRGenerator::downloadAllQrKaryawan');
   $routes->get('qr/karyawan/(:any)/download', 'Admin\QRGenerator::downloadQrKaryawan/$1');

   // admin buat laporan
   $routes->get('laporan', 'Admin\GenerateLaporan::index');
   $routes->post('laporan/siswa', 'Admin\GenerateLaporan::generateLaporanSiswa');
   $routes->post('laporan/karyawan', 'Admin\GenerateLaporan::generateLaporanKaryawan');

   // superadmin lihat data petugas
   $routes->get('petugas', 'Admin\DataPetugas::index');
   $routes->post('petugas', 'Admin\DataPetugas::ambilDataPetugas');
   // superadmin tambah data petugas
   $routes->get('petugas/register', 'Admin\DataPetugas::registerPetugas');
   // superadmin edit data petugas
   $routes->get('petugas/edit/(:any)', 'Admin\DataPetugas::formEditPetugas/$1');
   $routes->post('petugas/edit', 'Admin\DataPetugas::updatePetugas');
   // superadmin hapus data petugas
   $routes->delete('petugas/delete/(:any)', 'Admin\DataPetugas::delete/$1');

   // Settings
   $routes->group('general-settings', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
      $routes->get('/', 'GeneralSettings::index');
      $routes->post('update', 'GeneralSettings::generalSettingsPost');
   });
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
   require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
