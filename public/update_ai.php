<?php
header('Content-Type: text/plain');
$source = '/var/www/gkr_myid/python_services/build_index_new.py';
$dest = '/mnt/sdcard/ai-scanner/build_index.py';

if (file_exists($source)) {
    $content = file_get_contents($source);
    $result = file_put_contents($dest, $content);
    if ($result !== false) {
        echo "SUCCESS: Wrote $result bytes to $dest";
    } else {
        echo "ERROR: Failed to write to $dest. Check permissions.";
    }
} else {
    echo "ERROR: Source file not found!";
}
