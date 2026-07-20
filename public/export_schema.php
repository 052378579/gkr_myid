<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/../');
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';

$db = \Config\Database::connect();
$result = $db->query("DESCRIBE gkr_loguser")->getResultArray();
file_put_contents('schema_loguser.json', json_encode($result, JSON_PRETTY_PRINT));

$result2 = $db->query("DESCRIBE gkr_logcari")->getResultArray();
file_put_contents('schema_logcari.json', json_encode($result2, JSON_PRETTY_PRINT));

echo "Schema exported";
