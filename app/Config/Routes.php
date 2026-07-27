<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Beranda::index');
$routes->get('/force-migrate', 'MigrateController::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');
$routes->get('/daftar', 'Auth::daftar');
$routes->post('/daftar/process', 'Auth::processDaftar');

$routes->get('/profile', 'Profile::index');
$routes->post('/profile/update', 'Profile::update');
$routes->get('/dokumen/karyawan/(:segment)', 'Dokumen::karyawan/$1');
$routes->get('/dokumen/doodle/(:segment)', 'Dokumen::doodle/$1');
$routes->get('/dokumen/foto/(.*)', 'Dokumen::foto/$1');

$routes->get('/cari', 'Search::index');
$routes->get('/versi', 'Versi::index');

$routes->get('/admin/versi', '\App\Controllers\Admin\VersiController::index');
$routes->get('/admin/versi/getAll', '\App\Controllers\Api\VersiApi::getAll');
$routes->post('/admin/versi/store', '\App\Controllers\Api\VersiApi::store');
$routes->post('/admin/versi/update', '\App\Controllers\Api\VersiApi::update');
$routes->post('/admin/versi/delete', '\App\Controllers\Api\VersiApi::delete');
$routes->get('/admin', '\App\Controllers\Admin\AdminController::index');
$routes->get('/admin/dashboard', '\App\Controllers\Admin\AdminController::dashboard');
$routes->get('/admin/cari', '\App\Controllers\Admin\AdminController::cari');
$routes->get('/admin/doodle', '\App\Controllers\Admin\AdminController::doodle');
$routes->get('/admin/log', '\App\Controllers\Admin\AdminController::log');
$routes->get('/trend', 'TrendController::index');
$routes->get('/admin/crawl', '\App\Controllers\Admin\Crawler::index');
$routes->post('/crawler/doCrawl', 'Api\CrawlerApi::doCrawl');
$routes->post('/crawler/resetDb', 'Api\CrawlerApi::resetDb');

$routes->get('/admin/ai', '\App\Controllers\Admin\AiCrawler::index');
$routes->post('/admin/ai/doCrawl', '\App\Controllers\Admin\AiCrawler::doCrawl');

$routes->post('/api/updateLinkCount', 'Api\GraciaApi::updateLinkCount');
$routes->post('/api/updateImageCount', 'Api\GraciaApi::updateImageCount');
$routes->post('/api/setBroken', 'Api\GraciaApi::setBroken');
$routes->post('/api/dropCol', 'Api\GraciaApi::dropCol');
$routes->post('/api/storeSite', 'Api\GraciaApi::storeSite');
$routes->post('/api/storeImage', 'Api\GraciaApi::storeImage');
$routes->get('/api/setupDb', 'Api\GraciaApi::setupDb');
$routes->get('/api/materials', 'Api\GraciaApi::getMaterials');
$routes->get('/api/trendData', 'Api\GraciaApi::getTrendData');
$routes->get('/api/autocomplete', 'Api\GraciaApi::autocomplete');
$routes->get('/api/getMaterials', 'Api\GraciaApi::getMaterials');
$routes->get('/api/getSites', 'Api\GraciaApi::getSites');
$routes->get('/api/getImages', 'Api\GraciaApi::getImages');
$routes->get('/api/getTopSearched', 'Api\GraciaApi::getTopSearched');
$routes->get('/api/trend', 'Api\GraciaApi::getTrendData');
$routes->post('/api/deleteSite/(:num)', 'Api\GraciaApi::deleteSite/$1');
$routes->post('/api/deleteImage/(:num)', 'Api\GraciaApi::deleteImage/$1');
$routes->post('/api/updateSite/(:num)', 'Api\GraciaApi::updateSite/$1');
$routes->post('/api/updateImage/(:num)', 'Api\GraciaApi::updateImage/$1');
$routes->post('/api/search/upload', 'Api\ImageSearchApi::upload');

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

// Chatbot Webhook
$routes->post('/api/chatbot/webhook', '\App\Controllers\Api\ChatBotApi::webhook');
