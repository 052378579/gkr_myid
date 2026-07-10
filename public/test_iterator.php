<?php
$folderPath = '/var/www/FOTO/BUYER';
if (!is_dir($folderPath)) {
    echo "Not a dir";
    exit;
}
echo "Dir exists. \n";
try {
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST
    );
    $count = 0;
    foreach ($iterator as $item) {
        $count++;
        if ($count <= 5) {
            echo $item->getPathname() . "\n";
        }
    }
    echo "Total: $count\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
