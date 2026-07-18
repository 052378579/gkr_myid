<?php
header('Content-Type: text/plain');
if (file_exists('/mnt/sdcard/ai-scanner/main.py')) {
    echo file_get_contents('/mnt/sdcard/ai-scanner/main.py');
} else {
    echo "File not found!";
}
