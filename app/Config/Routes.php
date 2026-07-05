<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/cari', 'Search::index');
$routes->get('/admin', 'Admin::index');
$routes->get('/crawl', 'Crawler::index');
$routes->post('/crawler/doCrawl', 'Crawler::doCrawl');

$routes->post('/api/updateLinkCount', 'Api::updateLinkCount');
$routes->post('/api/updateImageCount', 'Api::updateImageCount');
$routes->post('/api/setBroken', 'Api::setBroken');
$routes->get('/api/getSites', 'Api::getSites');
$routes->get('/api/getImages', 'Api::getImages');
$routes->post('/api/deleteSite/(:num)', 'Api::deleteSite/$1');
$routes->post('/api/deleteImage/(:num)', 'Api::deleteImage/$1');
$routes->post('/api/updateSite/(:num)', 'Api::updateSite/$1');
$routes->post('/api/updateImage/(:num)', 'Api::updateImage/$1');
