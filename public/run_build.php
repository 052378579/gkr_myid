<?php
chdir('/mnt/sdcard/ai-scanner');
echo "Running build_index.py...\n";
$output = shell_exec('/mnt/sdcard/ai-scanner/env-ai/bin/python3 build_index.py 2>&1');
echo $output;

echo "\nRestarting AI Scanner service...\n";
$output2 = shell_exec('sudo systemctl restart ai_scanner.service 2>&1');
echo $output2;
echo "Done.";
