<?php
/**
 * Cafe Coffea – Cups & Moments
 * Database Connection & Application Configuration
 */

// Directory constants
define('ROOT_DIR', dirname(__DIR__));

$isVercel = (!empty($_ENV['VERCEL']) || !empty($_SERVER['VERCEL']) || getenv('VERCEL'));
if ($isVercel) {
    define('IS_VERCEL', true);
    define('DATA_DIR', '/tmp/data');
    define('SESSION_DIR', '/tmp/sessions');
    define('UPLOADS_DIR', '/tmp/uploads');
    define('DB_FILE', DATA_DIR . DIRECTORY_SEPARATOR . 'database.sqlite');
    
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0755, true);
    }
    // Copy bundled seed database if it doesn't exist in /tmp
    $sourceDb = ROOT_DIR . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'database.sqlite';
    if (!file_exists(DB_FILE) && file_exists($sourceDb)) {
        @copy($sourceDb, DB_FILE);
    }
} else {
    define('IS_VERCEL', false);
    define('DATA_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'data');
    define('UPLOADS_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads');
    define('DB_FILE', DATA_DIR . DIRECTORY_SEPARATOR . 'database.sqlite');
    define('SESSION_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'sessions');
}

// Ensure directories exist
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}
if (!is_dir(SESSION_DIR)) {
    @mkdir(SESSION_DIR, 0755, true);
}
if (!is_dir(UPLOADS_DIR)) {
    @mkdir(UPLOADS_DIR, 0755, true);
}
if (!is_dir(UPLOADS_DIR . DIRECTORY_SEPARATOR . 'photos')) {
    @mkdir(UPLOADS_DIR . DIRECTORY_SEPARATOR . 'photos', 0755, true);
}
if (!is_dir(UPLOADS_DIR . DIRECTORY_SEPARATOR . 'videos')) {
    @mkdir(UPLOADS_DIR . DIRECTORY_SEPARATOR . 'videos', 0755, true);
}

// Ensure session storage works reliably across all environments
if (session_status() === PHP_SESSION_NONE) {
    session_save_path(SESSION_DIR);
    session_start();
}

// Base URL detection helper
function get_base_url(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Script directory relative to document root
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $appDir = str_replace('\\', '/', ROOT_DIR);
    
    if ($docRoot && strpos($appDir, $docRoot) === 0) {
        $subDir = substr($appDir, strlen($docRoot));
        $subDir = '/' . trim($subDir, '/');
        if ($subDir === '/') {
            $subDir = '';
        }
    } else {
        $subDir = '';
    }
    
    return $protocol . "://" . $host . $subDir;
}

// Database singleton connection
function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $isNew = !file_exists(DB_FILE);
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Initialize schema and seed data if database is new or tables don't exist
        init_database($pdo);
    }
    return $pdo;
}

// Initialize tables and seed data
function init_database(PDO $pdo): void {
    // Settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        )
    ");

    // Photos table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS photos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            file_path TEXT NOT NULL,
            thumb_path TEXT,
            title TEXT,
            category TEXT DEFAULT 'general',
            display_order INTEGER DEFAULT 0,
            is_hero INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Videos table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS videos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            file_path TEXT NOT NULL,
            poster_path TEXT,
            title TEXT,
            description TEXT,
            display_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Instructions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS instructions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category TEXT NOT NULL,
            rule_number INTEGER DEFAULT 1,
            title TEXT,
            description TEXT NOT NULL,
            display_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1
        )
    ");

    // Admins table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT,
            password_hash TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Seed default settings if empty
    $count = (int)$pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($count === 0) {
        $defaultSettings = [
            'site_title' => 'Cafe Coffea – Cups & Moments',
            'brand_name' => 'Cafe Coffea',
            'brand_tagline' => 'Cups & Moments',
            'hero_heading' => 'Cups & Moments',
            'hero_subheading' => 'Celebrate Your Special Moments at Cafe Coffea.',
            'hero_description' => 'Create beautiful memories with your friends and family in our welcoming, premium party space in Kottakkal.',
            'hero_image' => 'assets/images/IMG_1994.jpg',
            'party_hall_price' => '1000',
            'cleaning_charge' => '200',
            'offer_threshold' => '5000',
            'offer_free_hours' => '1',
            'offer_headline' => 'Special Party Hall Privilege Offer',
            'offer_description' => 'When your eligible Cafe Coffea purchase amount reaches ₹5,000 or above, receive 1 hour of party hall usage completely FREE.',
            'offer_terms' => 'Eligible on food & beverage orders placed with Cafe Coffea for your celebration. The free party hall duration is strictly limited to 1 hour. Additional hours and cleaning charges apply as per standard terms.',
            'whatsapp_number' => '+918157022220',
            'phone_number' => '+91 81570 22220',
            'email' => 'contact@cafecoffea.in',
            'address' => 'Ground Floor, The Souk, Swagathamad, Kottakkal, Malappuram, Kerala 676510, India',
            'google_maps_url' => 'https://maps.google.com/?q=Cafe+Coffea+Kottakkal',
            'instagram_url' => 'https://instagram.com/cafecoffea',
            'official_website_url' => 'https://cafecoffea.in/',
            'currency_symbol' => '₹',
            'whatsapp_prefill_text' => 'Hello Cafe Coffea! I would like to enquire about party hall booking at Cafe Coffea – Cups & Moments.%0A• Date: %0A• Time Slot: %0A• Guest Count: %0A• Occasion: %0A• Cake Pre-booking: '
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (:key, :value)");
        foreach ($defaultSettings as $k => $v) {
            $stmt->execute([':key' => $k, ':value' => $v]);
        }
    }

    // Seed default admin if empty (admin / Coffea@2026!)
    $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($adminCount === 0) {
        $hash = password_hash('Coffea@2026!', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => 'admin',
            ':email' => 'admin@cafecoffea.in',
            ':password' => $hash
        ]);
    }

    // Seed default photos if empty
    $photoCount = (int)$pdo->query("SELECT COUNT(*) FROM photos")->fetchColumn();
    if ($photoCount === 0) {
        $initialPhotos = [
            ['file' => 'assets/images/IMG_1994.jpg', 'thumb' => 'assets/images/thumbs/IMG_1994.jpg', 'title' => 'Main Hall & Stage View', 'category' => 'interior', 'hero' => 1],
            ['file' => 'assets/images/IMG_1995.jpg', 'thumb' => 'assets/images/thumbs/IMG_1995.jpg', 'title' => 'Grand Hall Center Aisle', 'category' => 'interior', 'hero' => 0],
            ['file' => 'assets/images/IMG_1961.jpg', 'thumb' => 'assets/images/thumbs/IMG_1961.jpg', 'title' => 'Signature Stage & Golden Logo', 'category' => 'stage', 'hero' => 0],
            ['file' => 'assets/images/IMG_1964.jpg', 'thumb' => 'assets/images/thumbs/IMG_1964.jpg', 'title' => 'Party Stage Backdrop', 'category' => 'stage', 'hero' => 0],
            ['file' => 'assets/images/IMG_1965.jpg', 'thumb' => 'assets/images/thumbs/IMG_1965.jpg', 'title' => 'Celebration Stage Arch', 'category' => 'stage', 'hero' => 0],
            ['file' => 'assets/images/IMG_1966.jpg', 'thumb' => 'assets/images/thumbs/IMG_1966.jpg', 'title' => 'Stage Velvet Armchairs', 'category' => 'stage', 'hero' => 0],
            ['file' => 'assets/images/IMG_1967.jpg', 'thumb' => 'assets/images/thumbs/IMG_1967.jpg', 'title' => 'Arch & Drapes Closeup', 'category' => 'stage', 'hero' => 0],
            ['file' => 'assets/images/IMG_1980.jpg', 'thumb' => 'assets/images/thumbs/IMG_1980.jpg', 'title' => 'Warm Spotlight Ambience', 'category' => 'decor', 'hero' => 0],
            ['file' => 'assets/images/IMG_1988.jpg', 'thumb' => 'assets/images/thumbs/IMG_1988.jpg', 'title' => 'Guest Dining Arrangement', 'category' => 'seating', 'hero' => 0],
            ['file' => 'assets/images/IMG_1990.jpg', 'thumb' => 'assets/images/thumbs/IMG_1990.jpg', 'title' => 'Cozy Table Setup', 'category' => 'seating', 'hero' => 0],
            ['file' => 'assets/images/IMG_1992.jpg', 'thumb' => 'assets/images/thumbs/IMG_1992.jpg', 'title' => 'Modern Brick Wall Seating', 'category' => 'seating', 'hero' => 0],
            ['file' => 'assets/images/IMG_1996.jpg', 'thumb' => 'assets/images/thumbs/IMG_1996.jpg', 'title' => 'Dining Rows & Amber Lighting', 'category' => 'interior', 'hero' => 0],
            ['file' => 'assets/images/IMG_1999.jpg', 'thumb' => 'assets/images/thumbs/IMG_1999.jpg', 'title' => 'Spacious Party Hall Layout', 'category' => 'interior', 'hero' => 0],
            ['file' => 'assets/images/IMG_2002.jpg', 'thumb' => 'assets/images/thumbs/IMG_2002.jpg', 'title' => 'Celebration Dining Area', 'category' => 'seating', 'hero' => 0],
            ['file' => 'assets/images/IMG_2011.jpg', 'thumb' => 'assets/images/thumbs/IMG_2011.jpg', 'title' => 'Glass Gallery Perspective', 'category' => 'interior', 'hero' => 0],
            ['file' => 'assets/images/IMG_2012.jpg', 'thumb' => 'assets/images/thumbs/IMG_2012.jpg', 'title' => 'Panoramic Cafe Hall', 'category' => 'interior', 'hero' => 0],
            ['file' => 'assets/images/IMG_2013.jpg', 'thumb' => 'assets/images/thumbs/IMG_2013.jpg', 'title' => 'Intimate Seating Corner', 'category' => 'seating', 'hero' => 0],
            ['file' => 'assets/images/IMG_2014.jpg', 'thumb' => 'assets/images/thumbs/IMG_2014.jpg', 'title' => 'Warm Glow Cafe Evening', 'category' => 'decor', 'hero' => 0],
        ];

        $stmt = $pdo->prepare("INSERT INTO photos (file_path, thumb_path, title, category, display_order, is_hero, is_active) VALUES (:f, :t, :title, :cat, :ord, :hero, 1)");
        $order = 1;
        foreach ($initialPhotos as $p) {
            $stmt->execute([
                ':f' => $p['file'],
                ':t' => $p['thumb'],
                ':title' => $p['title'],
                ':cat' => $p['category'],
                ':ord' => $order++,
                ':hero' => $p['hero']
            ]);
        }
    }

    // Seed default videos if empty
    $videoCount = (int)$pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
    if ($videoCount === 0) {
        $initialVideos = [
            [
                'file' => 'assets/videos/IMG_1974.MOV',
                'poster' => 'assets/images/thumbs/IMG_1961.jpg',
                'title' => 'Party Hall Walkthrough & Stage Tour',
                'description' => 'Experience the welcoming atmosphere, designer party stage, ambient lighting and spacious seating in our exclusive party hall.'
            ],
            [
                'file' => 'assets/videos/IMG_1981.MOV',
                'poster' => 'assets/images/thumbs/IMG_1994.jpg',
                'title' => 'Ambiance & Seating Highlights',
                'description' => 'A detailed look at the elegant furnishings, warm amber pendant glow and celebration layout at Cafe Coffea.'
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO videos (file_path, poster_path, title, description, display_order, is_active) VALUES (:f, :p, :t, :d, :ord, 1)");
        $order = 1;
        foreach ($initialVideos as $v) {
            $stmt->execute([
                ':f' => $v['file'],
                ':p' => $v['poster'],
                ':t' => $v['title'],
                ':d' => $v['description'],
                ':ord' => $order++
            ]);
        }
    }

    // Seed default instructions if empty
    $instructionCount = (int)$pdo->query("SELECT COUNT(*) FROM instructions")->fetchColumn();
    if ($instructionCount === 0) {
        $instructions = [
            // Party Hall Rules
            ['category' => 'rules', 'rule' => 1, 'title' => 'Standard Party Hall Charge', 'desc' => 'Standard party hall charge is ₹1,000 for one hour.'],
            ['category' => 'rules', 'rule' => 2, 'title' => 'Cleaning Charge', 'desc' => 'Cleaning charge is ₹200 per booking.'],
            ['category' => 'rules', 'rule' => 3, 'title' => 'Standard Duration', 'desc' => 'Standard party hall duration is exactly one hour.'],
            ['category' => 'rules', 'rule' => 4, 'title' => 'Punctuality & Schedule', 'desc' => 'Customers must follow the allotted time strictly.'],
            ['category' => 'rules', 'rule' => 5, 'title' => 'Additional Hours & Approvals', 'desc' => 'Additional usage requires prior approval and applicable charges.'],

            // Decoration Policy
            ['category' => 'decoration', 'rule' => 1, 'title' => 'Own Decoration Arrangements', 'desc' => 'Customers are allowed to arrange their own decorations.'],
            ['category' => 'decoration', 'rule' => 2, 'title' => 'Wallpaper Protection', 'desc' => 'Customers must ensure that decorations do not damage the wallpaper under any circumstances.'],
            ['category' => 'decoration', 'rule' => 3, 'title' => 'Property Care', 'desc' => 'Do not damage walls, furniture, lighting, fixtures or other café property.'],
            ['category' => 'decoration', 'rule' => 4, 'title' => 'Safe Decoration Methods', 'desc' => 'Use safe decoration methods (such as non-marking adhesive clips or free-standing stands) that can be removed without causing damage.'],
            ['category' => 'decoration', 'rule' => 5, 'title' => 'Customer Responsibility', 'desc' => 'Customers are solely responsible for any damage caused during their event.'],
            ['category' => 'decoration', 'rule' => 6, 'title' => 'Management Inspection', 'desc' => 'If any damage occurs, the café management will inspect it immediately following the event.'],
            ['category' => 'decoration', 'rule' => 7, 'title' => 'Repair & Replacement Assessment', 'desc' => 'The necessary repair or replacement charges will be assessed and communicated to the customer for prompt settlement.'],
            ['category' => 'decoration', 'rule' => 8, 'title' => 'Record of Damages', 'desc' => 'Management maintains an official record of damages and applicable charges.'],

            // Cake Policy
            ['category' => 'cake', 'rule' => 1, 'title' => 'Cafe Coffea Cake Pre-Booking', 'desc' => 'Customers can pre-book exquisite handcrafted cakes directly from Cafe Coffea.'],
            ['category' => 'cake', 'rule' => 2, 'title' => 'In-House Cake Pricing', 'desc' => 'Cakes prepared by Cafe Coffea have separate transparent menu prices based on flavor and size.'],
            ['category' => 'cake', 'rule' => 3, 'title' => 'Outside Cake Permitted', 'desc' => 'Customers may also bring their own celebration cake from outside.'],
            ['category' => 'cake', 'rule' => 4, 'title' => 'Outside Food Restriction', 'desc' => 'Other outside food items and beverages are strictly not allowed.'],
            ['category' => 'cake', 'rule' => 5, 'title' => 'Advance Cake Notice', 'desc' => 'Customers should inform the café in advance about their cake requirements and timing.'],
        ];

        $stmt = $pdo->prepare("INSERT INTO instructions (category, rule_number, title, description, display_order, is_active) VALUES (:cat, :rn, :t, :d, :ord, 1)");
        $order = 1;
        foreach ($instructions as $ins) {
            $stmt->execute([
                ':cat' => $ins['category'],
                ':rn' => $ins['rule'],
                ':t' => $ins['title'],
                ':d' => $ins['desc'],
                ':ord' => $order++
            ]);
        }
    }
}

// Helper to fetch all settings as an associative key-value map
function get_all_settings(): array {
    $db = get_db();
    $rows = $db->query("SELECT key, value FROM settings")->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }
    return $settings;
}

// Helper to get a single setting with fallback
function get_setting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        $settings = get_all_settings();
    }
    return $settings[$key] ?? $default;
}

// Authentication helpers
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id']);
}

function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        $baseUrl = get_base_url();
        header("Location: " . $baseUrl . "/admin/login.php");
        exit;
    }
}

// CSRF token helpers
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize string for HTML output
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
