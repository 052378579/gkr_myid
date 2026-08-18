<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * AUTO-DETECT ENVIRONMENT & DYNAMIC CONFIG (DEV / PROD)
 *---------------------------------------------------------------
 */
$host     = $_SERVER['HTTP_HOST'] ?? 'cli';
$port     = $_SERVER['SERVER_PORT'] ?? '';
$serverIp = $_SERVER['SERVER_ADDR'] ?? ''; // Keamanan tambahan: IP Fisik Server

// 1. DEV ZONE (Hak Akses Tertutup: ZeroTier, LAN, Spark, CLI)
if (
    strpos($serverIp, '10.147.17.40') !== false || 
    strpos($serverIp, '192.168.1.4') !== false || 
    $port === '8000' || 
    $port === '8080' ||
    $host === 'cli' ||
    strpos($host, '10.147.17.40') !== false
) {
    // Set Environment
    $_SERVER['CI_ENVIRONMENT'] = 'development';
    
    // Inject Config App
    $_SERVER['app.baseURL'] = 'http://' . ($host !== 'cli' ? $host : 'localhost') . '/';
    $_SERVER['app.forceGlobalSecureRequests'] = 'false';

    // Inject Config Database (DEV)
    $_SERVER['database.default.hostname'] = 'localhost';
    $_SERVER['database.default.database'] = 'gkr_myid';
    $_SERVER['database.default.username'] = 'root';
    $_SERVER['database.default.password'] = '102013';
} 
// 2. PROD ZONE (Hak Akses Publik via Domain)
elseif (
    strpos($host, 'budi.biz.id') !== false || 
    strpos($host, 'gkr.my.id') !== false ||
    strpos($serverIp, '192.168.1.17') !== false
) {
    // Set Environment
    $_SERVER['CI_ENVIRONMENT'] = 'production';
    
    // Inject Config App
    $_SERVER['app.baseURL'] = 'https://' . $host . '/';
    $_SERVER['app.forceGlobalSecureRequests'] = 'true';

    // Inject Config Database (PROD) - dibaca dari .env PROD (gitignored, aman dari Git)
    // .env PROD diedit manual via SSH; tidak terpengaruh git reset --hard maupun git clean -fd
    $_SERVER['database.default.hostname'] = getenv('DB_PROD_HOST') ?: 'localhost';
    $_SERVER['database.default.database'] = getenv('DB_PROD_NAME') ?: 'gkr_myid';
    $_SERVER['database.default.username'] = getenv('DB_PROD_USER') ?: 'root';
    $_SERVER['database.default.password'] = getenv('DB_PROD_PASS') ?: '102013';
} 
// 3. SECURE FALLBACK (Sapu Jagat Perlindungan dari Bot/Scanner IP Publik)
else {
    $_SERVER['CI_ENVIRONMENT'] = 'production';
    
    // Fallback Config (Mencegah error jika masuk sini)
    $_SERVER['app.baseURL'] = 'https://gkr.my.id/';
    $_SERVER['app.forceGlobalSecureRequests'] = 'true';
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));