<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Base Site URL
     * --------------------------------------------------------------------------
     *
     * URL to your CodeIgniter root. Typically this will be your base URL,
     * WITH a trailing slash:
     *
     * E.g., http://example.com/
     *
     * @var string
     */
    public string $baseURL = 'https://gkr.budi.biz.id/';

    /**
     * Konstruktor Dinamis: 
     * Mendeteksi BaseURL secara otomatis berdasarkan host/IP yang diakses, 
     * memecahkan masalah CORS Debug Toolbar di lingkungan Dev/Prod multi-akses.
     */
    public function __construct()
    {
        parent::__construct();
        
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                         (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) 
                         ? 'https://' : 'http://';
            $this->baseURL = $protocol . $_SERVER['HTTP_HOST'] . '/';
        }
    }
    /**
     * Allowed Hostnames in the Site URL other than the hostname in the baseURL.
     * If you want to accept multiple Hostnames, set this.
     *
     * E.g.
     * When your site URL ($baseURL) is 'http://example.com/', and your site
     * also accepts 'http://media.example.com/' and 'http://accounts.example.com/':
     *     ['media.example.com', 'accounts.example.com']
     *
     * @var list<string>
     */
    public array $allowedHostnames = [];

    /**
     * --------------------------------------------------------------------------
     * Index File
     * --------------------------------------------------------------------------
     *
     * Typically this will be your `index.php` file, unless you've renamed it to
     * something else. If you have configured your web server to remove this file
     * from your site URIs, set this variable to an empty string.
     *
     * @var string
     */
    public string $indexPage = '';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     *
     * This item determines which server global should be used to retrieve the
     * URI string. The default setting of 'REQUEST_URI' works for most servers.
     * If your links do not seem to work, try one of the other delicious flavors:
     *
     *  'REQUEST_URI': Uses $_SERVER['REQUEST_URI']
     * 'QUERY_STRING': Uses $_SERVER['QUERY_STRING']
     *    'PATH_INFO': Uses $_SERVER['PATH_INFO']
     *
     * WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
     *
     * @var string
     */
    public string $uriProtocol = 'REQUEST_URI';

    /**
     * --------------------------------------------------------------------------
     * Default Locale
     * --------------------------------------------------------------------------
     *
     * The Locale roughly represents the language and location that your visitor
     * is viewing the site from. It affects the language strings and other
     * strings (like currency markers, numbers, etc), that your program
     * should run under for this request.
     *
     * @var string
     */
    public string $defaultLocale = 'id';

    /**
     * --------------------------------------------------------------------------
     * Negotiate Locale
     * --------------------------------------------------------------------------
     *
     * If true, the current Request object will automatically determine the
     * language to use based on the value of the Accept-Language header.
     *
     * If false, no automatic detection will be performed.
     *
     * @var bool
     */
    public bool $negotiateLocale = false;

    /**
     * --------------------------------------------------------------------------
     * Supported Locales
     * --------------------------------------------------------------------------
     *
     * If $negotiateLocale is true, this array lists the locales supported
     * by the application in descending order of priority. If no match is
     * found, the first locale will be used.
     *
     * @var string[]
     */
    public array $supportedLocales = ['id'];

    /**
     * --------------------------------------------------------------------------
     * Application Timezone
     * --------------------------------------------------------------------------
     *
     * The default timezone that will be used in your application to display
     * dates with the date helper, and can be retrieved through app_timezone()
     *
     * @var string
     */
    public string $appTimezone = 'Asia/Jakarta';

    /**
     * --------------------------------------------------------------------------
     * Default Character Set
     * --------------------------------------------------------------------------
     *
     * This determines which character set is used by default in various methods
     * that require a character set to be provided.
     *
     * @see http://php.net/htmlspecialchars for a list of supported charsets.
     *
     * @var string
     */
    public string $charset = 'UTF-8';

    /**
     * --------------------------------------------------------------------------
     * Force Global Secure Requests
     * --------------------------------------------------------------------------
     *
     * If true, this will force every request made to this application to be
     * made via a secure connection (HTTPS). If the incoming request is not
     * secure, the user will be redirected to a secure version of the page
     * and the HTTP Strict Transport Security header will be set.
     *
     * @var bool
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * --------------------------------------------------------------------------
     * Reverse Proxy IPs
     * --------------------------------------------------------------------------
     *
     * Jika aplikasi berada di balik Reverse Proxy (Nginx, ZeroTier, Docker), 
     * kita harus mendaftarkan subnet IP proxy tersebut agar CodeIgniter 
     * membaca header HTTP_X_FORWARDED_FOR untuk mendapatkan IP asli pengunjung.
     *
     * @var list<string>
     */
    public array $proxyIPs = [
        '10.0.0.0/8'     => 'X-Forwarded-For', // Rentang Private IP (ZeroTier, Docker, Tunnels)
        '192.168.0.0/16' => 'X-Forwarded-For', // Rentang Private IP (LAN)
        '172.16.0.0/12'  => 'X-Forwarded-For', // Rentang Private IP (Docker / Tunnels)
        '127.0.0.1'      => 'X-Forwarded-For', // Localhost Nginx/Apache Proxy
        '::1'            => 'X-Forwarded-For'
    ];

    /**
     * --------------------------------------------------------------------------
     * Content Security Policy
     * --------------------------------------------------------------------------
     *
     * Enables the Response's Content Secure Policy to restrict the sources that
     * can be used for images, scripts, CSS files, audio, video, etc. If enabled,
     * the Response object will populate default values for the policy from the
     * `Config\ContentSecurityPolicy.php` file. Controllers can always add to
     * those restrictions at run time.
     *
     * For a better understanding of CSP, see these documents:
     *
     * @see http://www.html5rocks.com/en/tutorials/security/content-security-policy/
     * @see http://www.w3.org/TR/CSP/
     *
     * @var bool
     */
    public bool $CSPEnabled = false;

    /**
     * --------------------------------------------------------------------------
     * Allowed URL Characters
     * --------------------------------------------------------------------------
     *
     * This lets you specify which characters are permitted within your URLs.
     * When someone tries to submit a URL with disallowed characters they will
     * get a warning message.
     *
     * As a security measure you are STRONGLY encouraged to restrict URLs to
     * as few characters as possible. By default only these are allowed: a-z 0-9~%.:_-
     *
     * Leave blank to allow all characters -- but only if you are insane.
     *
     * The configured value is actually a regular expression character group
     * and it will be executed as: ! preg_match('/^[' . $permittedURIChars . ']+$/i
     *
     * DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
     *
     * @var string
     */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-';
}
