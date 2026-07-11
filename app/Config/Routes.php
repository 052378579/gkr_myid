<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
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
$routes->get('/admin/versi', 'AdminVersi::index');
$routes->get('/admin/versi/getAll', 'AdminVersi::getAll');
$routes->post('/admin/versi/store', 'AdminVersi::store');
$routes->post('/admin/versi/update', 'AdminVersi::update');
$routes->post('/admin/versi/delete', 'AdminVersi::delete');
$routes->get('/admin', 'Admin::index');
$routes->get('/crawl', 'Crawler::index');
$routes->post('/crawler/doCrawl', 'Crawler::doCrawl');
$routes->post('/crawler/resetDb', 'Crawler::resetDb');

$routes->post('/api/updateLinkCount', 'Api::updateLinkCount');
$routes->post('/api/updateImageCount', 'Api::updateImageCount');
$routes->post('/api/setBroken', 'Api::setBroken');
$routes->get('/api/getSites', 'Api::getSites');
$routes->get('/api/getImages', 'Api::getImages');
$routes->post('/api/deleteSite/(:num)', 'Api::deleteSite/$1');
$routes->post('/api/deleteImage/(:num)', 'Api::deleteImage/$1');
$routes->post('/api/updateSite/(:num)', 'Api::updateSite/$1');
$routes->post('/api/updateImage/(:num)', 'Api::updateImage/$1');

$routes->get('/doodle/getAll', 'Admin\DoodleController::getAll');
$routes->post('/doodle/store', 'Admin\DoodleController::store');
$routes->post('/doodle/update', 'Admin\DoodleController::update');
$routes->post('/doodle/delete', 'Admin\DoodleController::delete');
$routes->post('/doodle/generateRecurring', 'Admin\DoodleController::generateRecurring');
// Karyawan (Pengguna) CRUD
$routes->get('/admin/karyawan', 'Admin\KaryawanController::index');
$routes->get('/admin/karyawan/getAll', 'Admin\KaryawanController::getAll');
$routes->post('/admin/karyawan/store', 'Admin\KaryawanController::store');
$routes->post('/admin/karyawan/update', 'Admin\KaryawanController::update');
$routes->post('/admin/karyawan/delete', 'Admin\KaryawanController::delete');
