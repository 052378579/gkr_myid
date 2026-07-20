<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/../');
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

// Drop old tables
$forge->dropTable('gkr_loguser', true);
$forge->dropTable('gkr_logcari', true);

// Remove from migrations table
$db->table('migrations')->like('class', 'CreateLogTables')->delete();

echo "Tables dropped and migration record deleted.\n";
