<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Beranda::index');
$routes->get('/force-migrate', 'MigrateController::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

$routes->get('/profile', 'Profile::index');
$routes->post('/profile/update', 'Profile::update');
$routes->get('/dokumen/karyawan/(:segment)', 'Dokumen::karyawan/$1');
$routes->get('/dokumen/doodle/(:segment)', 'Dokumen::doodle/$1');
$routes->get('/dokumen/foto/(.*)', 'Dokumen::foto/$1');

$routes->get('/cari', 'Search::index');
$routes->get('/versi', 'Versi::index');

$routes->get('/admin/versi', '\App\Controllers\Admin\VersiController::index');
$routes->get('/admin/versi/getAll', '\App\Controllers\Admin\VersiController::getAll');
$routes->post('/admin/versi/store', '\App\Controllers\Admin\VersiController::store');
$routes->post('/admin/versi/update', '\App\Controllers\Admin\VersiController::update');
$routes->post('/admin/versi/delete', '\App\Controllers\Admin\VersiController::delete');
$routes->get('/admin', '\App\Controllers\Admin\AdminController::index');
$routes->get('/admin/doodle', '\App\Controllers\Admin\AdminController::doodle');
$routes->get('/admin/log', '\App\Controllers\Admin\AdminController::log');
$routes->get('/trend', 'TrendController::index');
$routes->get('/admin/crawl', 'Crawler::index');
$routes->post('/crawler/doCrawl', 'Crawler::doCrawl');
$routes->post('/crawler/resetDb', 'Crawler::resetDb');

$routes->get('/crawl/ai', 'AiCrawler::index');
$routes->post('/crawl/ai/doCrawl', 'AiCrawler::doCrawl');

$routes->post('/api/updateLinkCount', 'Api::updateLinkCount');
$routes->post('/api/updateImageCount', 'Api::updateImageCount');
$routes->post('/api/setBroken', 'Api::setBroken');
$routes->get('/api/setupDb', 'Api::setupDb');
$routes->get('/api/dropCol', 'Api::dropCol');
$routes->post('/api/storeSite', 'Api::storeSite');
$routes->post('/api/storeImage', 'Api::storeImage');
$routes->get('/api/getMaterials', 'Api::getMaterials');
$routes->get('/api/getSites', 'Api::getSites');
$routes->get('/api/getImages', 'Api::getImages');
$routes->get('/api/trend', 'Api::getTrendData');
$routes->post('/api/deleteSite/(:num)', 'Api::deleteSite/$1');
$routes->post('/api/deleteImage/(:num)', 'Api::deleteImage/$1');
$routes->post('/api/updateSite/(:num)', 'Api::updateSite/$1');
$routes->post('/api/updateImage/(:num)', 'Api::updateImage/$1');
$routes->get('/api/insertVersion', 'InsertVersion::index');
$routes->post('/api/search/upload', 'ImageSearchApi::upload');

$routes->get('/doodle/getAll', 'Admin\DoodleController::getAll');
$routes->post('/doodle/store', 'Admin\DoodleController::store');
$routes->post('/doodle/update', 'Admin\DoodleController::update');
$routes->post('/doodle/delete', 'Admin\DoodleController::delete');
$routes->post('/doodle/generateRecurring', 'Admin\DoodleController::generateRecurring');
// Karyawan (Pengguna) CRUD
$routes->get('/admin/karyawan', '\App\Controllers\Admin\KaryawanController::index');
$routes->get('/admin/karyawan/getAll', '\App\Controllers\Admin\KaryawanController::getAll');
$routes->post('/admin/karyawan/store', '\App\Controllers\Admin\KaryawanController::store');
$routes->post('/admin/karyawan/update', '\App\Controllers\Admin\KaryawanController::update');
$routes->post('/admin/karyawan/delete', '\App\Controllers\Admin\KaryawanController::delete');
