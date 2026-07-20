<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/../');
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$migrate = \Config\Services::migrations();
try {
    $migrate->latest();
    echo "SUCCESS";
} catch (\Throwable $e) {
    file_put_contents('migration_error.txt', $e->getMessage());
    echo "ERROR SAVED";
}
