<?php
header('Content-Type: application/json');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/../');
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\/ ') . '/bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$spell = new \App\Libraries\SpellChecker();
$q = $_GET['q'] ?? 'AMALA';
echo json_encode([
    'query' => $q,
    'suggestion' => $spell->getCorrection($q),
    'dictionary_sample' => array_slice(\Config\Services::cache()->get('search_dictionary_words_v4') ?? [], 0, 100)
]);
