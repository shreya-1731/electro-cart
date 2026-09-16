<?php
// config/db.php - Database connection & auto-initializer

define('DB_FILE', __DIR__ . '/../data/electro_cart.db');

function getDb(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $isNew = !file_exists(DB_FILE);
        $dir = dirname(DB_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($isNew || filesize(DB_FILE) === 0) {
            initDatabase($pdo);
        }
    }
    return $pdo;
}

function initDatabase(PDO $pdo): void {
    // Categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        icon TEXT,
        description TEXT
    )");

    // Products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        short_desc TEXT,
        description TEXT,
        specs TEXT,
        price REAL NOT NULL,
        old_price REAL,
        rating REAL DEFAULT 4.8,
        reviews_count INTEGER DEFAULT 24,
        stock INTEGER DEFAULT 15,
        image_url TEXT NOT NULL,
        badge TEXT,
        is_featured INTEGER DEFAULT 0,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )");

    // Orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT UNIQUE NOT NULL,
        customer_name TEXT NOT NULL,
        customer_email TEXT NOT NULL,
        phone TEXT,
        address TEXT NOT NULL,
        city TEXT,
        zip TEXT,
        payment_method TEXT NOT NULL,
        subtotal REAL NOT NULL,
        discount REAL DEFAULT 0,
        shipping REAL DEFAULT 0,
        tax REAL DEFAULT 0,
        total REAL NOT NULL,
        status TEXT DEFAULT 'Confirmed',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Order items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        product_name TEXT NOT NULL,
        price REAL NOT NULL,
        quantity INTEGER NOT NULL,
        total REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )");

    // Seed Categories (5 categories)
    $categories = [
        [1, 'Laptops & Ultrabooks', 'laptops', 'fa-laptop', 'Flagship workstations, lightweight ultrabooks and ultra gaming laptops.'],
        [2, 'Smartphones & Tablets', 'smartphones', 'fa-mobile-screen', 'Next-generation folding displays, flagship cameras and pro tablets.'],
        [3, 'Audio & Headphones', 'audio', 'fa-headphones', 'Audiophile studio sound, ANC wireless headphones and immersive acoustics.'],
        [4, 'Wearables & Smart IoT', 'wearables', 'fa-clock', 'Titanium smartwatches, biometric health rings and futuristic AR optics.'],
        [5, 'Gaming & Gear', 'gaming', 'fa-gamepad', 'Mechanical keyboards, ultra-light precision mice and next-gen VR headsets.']
    ];

    $catStmt = $pdo->prepare("INSERT OR IGNORE INTO categories (id, name, slug, icon, description) VALUES (?, ?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $catStmt->execute($cat);
    }

    // Seed 20 Products (4 per category)
    $products = [
        // Category 1: Laptops (1-4)
        [
            1, 1, 'ApexBook Pro 16 Max', 'apexbook-pro-16-max',
            '16.2" Liquid Retina XDR, M3 Max 16-Core, 48GB Unified RAM, 1TB NVMe SSD',
            'Engineered for extreme performance and boundless creativity. The ApexBook Pro 16 Max features the fastest mobile silicon ever crafted, offering whisper-quiet thermal efficiency, up to 22 hours of battery life, and studio-grade audio.',
            json_encode([
                'Processor' => 'M3 Max 16-Core CPU / 40-Core GPU',
                'Memory' => '48GB Unified High-Bandwidth Memory',
                'Display' => '16.2-inch Liquid Retina XDR (3456x2234, 120Hz ProMotion)',
                'Storage' => '1TB Ultra-Fast NVMe SSD',
                'Battery Life' => 'Up to 22 Hours Web / 15 Hours Render',
                'Weight' => '2.14 kg (Titanium chassis)'
            ]),
            2399.00, 2599.00, 4.9, 142, 12, 'assets/images/products/p1.jpg', 'Flagship', 1
        ],
        [
            2, 1, 'Zenith Ultralight 14', 'zenith-ultralight-14',
            '14" 2.8K 120Hz OLED Touch, Intel Core Ultra 9, 32GB LPDDR5X, 1TB SSD',
            'Ultra-thin, featherweight engineering crafted from CNC aerospace magnesium alloy. Features an edge-to-edge OLED touchscreen with 100% DCI-P3 color accuracy and integrated neural processing unit.',
            json_encode([
                'Processor' => 'Intel Core Ultra 9 185H (16 Cores, NPU AI Engine)',
                'Memory' => '32GB LPDDR5X 7467MHz',
                'Display' => '14" 2.8K (2880x1800) OLED, 120Hz 0.2ms HDR500',
                'Storage' => '1TB PCIe 4.0 M.2 SSD',
                'Battery Life' => 'Up to 16 Hours',
                'Weight' => '1.18 kg'
            ]),
            1599.00, 1749.00, 4.8, 89, 18, 'assets/images/products/p2.jpg', 'Best Seller', 1
        ],
        [
            3, 1, 'Titan Blade RTX 4080', 'titan-blade-rtx-4080',
            '17.3" QHD 240Hz, AMD Ryzen 9 7945HX, RTX 4080 12GB, 32GB DDR5, 2TB SSD',
            'Dominant desktop-class gaming powerhouse in a stealth chassis. Vapor-chamber liquid metal cooling ensures sustained clock speeds at ultra graphic presets without thermal throttling.',
            json_encode([
                'Processor' => 'AMD Ryzen 9 7945HX (16 Cores, 32 Threads, 5.4GHz)',
                'Graphics' => 'NVIDIA GeForce RTX 4080 12GB GDDR6 (175W TGP)',
                'Display' => '17.3" QHD (2560x1440) 240Hz 3ms G-Sync',
                'Storage' => '2TB PCIe Gen4 NVMe RAID 0',
                'Cooling' => 'Dual Vapor-Chamber Liquid Metal'
            ]),
            2799.00, 2999.00, 4.9, 64, 7, 'assets/images/products/p3.jpg', 'Hot', 1
        ],
        [
            4, 1, 'AeroBook Air M2', 'aerobook-air-m2',
            '13.6" Liquid Retina, Fanless Silent Design, 16GB Unified, 512GB SSD',
            'Incredibly thin and fast. The fanless design ensures totally silent operation even when rendering 4K streams or multi-tasking across dozens of applications.',
            json_encode([
                'Processor' => 'Next-Gen 8-Core CPU / 10-Core GPU',
                'Memory' => '16GB Unified Memory',
                'Display' => '13.6" Liquid Retina Display (500 nits, P3 Wide Color)',
                'Storage' => '512GB SSD Storage',
                'Thickness' => '11.3 mm Ultra-Slim'
            ]),
            1099.00, 1199.00, 4.7, 210, 25, 'assets/images/products/p4.jpg', 'Sale', 0
        ],

        // Category 2: Smartphones & Tablets (5-8)
        [
            5, 2, 'Nova Ultra 5G Titanium', 'nova-ultra-5g-titanium',
            '6.8" Dynamic AMOLED 2X 120Hz, 200MP Quad Cam, 5000mAh, Titanium Gray',
            'Forged in grade 5 titanium with an anti-reflective flat armor glass screen. Includes an ultra-precision embedded stylus and groundbreaking on-device generative AI photography suite.',
            json_encode([
                'Display' => '6.8" Dynamic AMOLED 2X QHD+ (1-120Hz LTPO, 2600 nits)',
                'Camera' => '200MP Main + 50MP 5x Periscope + 12MP Ultra-Wide',
                'Processor' => 'Snapdragon 8 Gen 3 for Galaxy (4nm)',
                'Battery' => '5,000 mAh with 45W Fast Super Charging',
                'Build' => 'Titanium Frame + Gorilla Armor Glass'
            ]),
            1199.00, 1299.00, 4.9, 312, 20, 'assets/images/products/p5.jpg', 'Top Pick', 1
        ],
        [
            6, 2, 'Aura Horizon Fold Pro', 'aura-horizon-fold-pro',
            '7.9" Internal Folding OLED + 6.4" External OLED, Zero-Gap Hinge, 16GB/512GB',
            'Unfold boundless productivity. Zero-crease engineered aerospace hinge folds completely flat. Multi-window split screen lets you execute three apps simultaneously.',
            json_encode([
                'Displays' => 'Inner 7.9" Flexible OLED 120Hz / Outer 6.4" OLED 120Hz',
                'Hinge' => 'Zero-Gap Armor Aluminum Drop Hinge (400,000 folds)',
                'Camera' => '50MP Dual OIS Triple Lens Array',
                'Memory/Storage' => '16GB LPDDR5X + 512GB UFS 4.0',
                'Water Resistance' => 'IPX8 Certified'
            ]),
            1699.00, 1899.00, 4.8, 95, 8, 'assets/images/products/p6.jpg', 'Futuristic', 1
        ],
        [
            7, 2, 'ProTab Studio 13', 'protab-studio-13',
            '13" Ultra Retina Tandem OLED, M4 Chip, 256GB, Apple Pencil Pro Support',
            'The thinnest high-performance creative tablet ever made. Tandem OLED technology combines the light from two OLED panels for jaw-dropping brightness and extreme HDR contrast.',
            json_encode([
                'Display' => '13" Ultra Retina Tandem OLED (1000 nits full screen)',
                'Processor' => 'M4 9-Core Chip with 16-Core Neural Engine',
                'Storage' => '256GB High-Speed Flash',
                'Thickness' => '5.1 mm Thinnest Device',
                'Audio' => 'Four-speaker sound system with Studio Mics'
            ]),
            899.00, 999.00, 4.8, 178, 14, 'assets/images/products/p7.jpg', 'Popular', 0
        ],
        [
            8, 2, 'PixelGlide X Pure', 'pixelglide-x-pure',
            '6.7" Super Actua OLED, Pure AI Camera Engine, 12GB RAM, Obsidian Black',
            'Pure Android experience with 7 years of OS feature drops. Magic Editor, Best Take audio isolation, and custom Tensor chip provide real-time translation and computational photography.',
            json_encode([
                'Processor' => 'Google Tensor G3 Security Co-Processor',
                'Camera' => '50MP Octa PD + 48MP Quad PD 5x Telephoto',
                'Display' => '6.7" Super Actua display (1-120 Hz, up to 2400 nits)',
                'OS Support' => '7 Years guaranteed OS & Security Updates'
            ]),
            799.00, 899.00, 4.7, 124, 16, 'assets/images/products/p8.jpg', 'Sale', 0
        ],

        // Category 3: Audio & Headphones (9-12)
        [
            9, 3, 'SonicWave Pro ANC', 'sonicwave-pro-anc',
            'Hybrid Active Noise Cancellation, Lossless High-Res Audio, 40H Battery',
            'Mastered for audiophiles. 40mm custom graphene drivers deliver punchy sub-bass, silky vocals, and crystal highs. Real-time active noise cancellation neutralizes 98% of ambient noise.',
            json_encode([
                'Drivers' => '40mm Custom Tuned Graphene Dynamic Drivers',
                'ANC' => 'Dual-Feedback Hybrid Active Noise Cancellation',
                'Codecs' => 'LDAC, AAC, aptX Lossless, SBC',
                'Battery Life' => '40 Hours ANC On / 60 Hours ANC Off',
                'Weight' => '245g Ultralight Comfort'
            ]),
            349.00, 399.00, 4.9, 280, 22, 'assets/images/products/p9.jpg', 'Best Sound', 1
        ],
        [
            10, 3, 'AeroBuds Studio Wireless', 'aerobuds-studio-wireless',
            'Spatial Audio with Head Tracking, Adaptive Transparency, Wireless Qi Case',
            'Next-generation true wireless earbuds featuring personalized spatial acoustic profiling. Custom silicone tips with acoustic venting provide all-day comfort and pressure equalization.',
            json_encode([
                'Spatial Audio' => 'Dynamic Head-Tracking 3D Audio',
                'Noise Control' => 'Adaptive Audio & Personalized Volume',
                'Battery' => '6 Hours per charge, 30 Hours with MagSafe Case',
                'Microphones' => 'Triple Beamforming microphones with Wind Mesh'
            ]),
            199.00, 229.00, 4.8, 410, 30, 'assets/images/products/p10.jpg', 'Hot', 1
        ],
        [
            11, 3, 'BassForge Studio Soundbar', 'bassforge-studio-soundbar',
            '5.1.2 Dolby Atmos Soundbar with 8" Wireless Subwoofer & E-ARC HDMI',
            'Cinematic multi-channel soundbar engineered with up-firing height channels to reflect audio off ceilings, creating true 3D spatial envelopment for movies and gaming.',
            json_encode([
                'Channels' => '5.1.2 Dolby Atmos / DTS:X certified',
                'Subwoofer' => '8-inch Long-Throw Wireless Subwoofer (250W)',
                'Connectivity' => 'HDMI eARC, Optical, AirPlay 2, Spotify Connect',
                'Total Power' => '550 Watts Peak Power'
            ]),
            499.00, 549.00, 4.7, 72, 10, 'assets/images/products/p11.jpg', 'Cinema', 0
        ],
        [
            12, 3, 'PulseRetro Hi-Fi Speaker', 'pulseretro-hi-fi-speaker',
            'Handcrafted Walnut & Brass Bluetooth 5.3 Speaker, Analog EQ Knobs',
            'Vintage mid-century aesthetic merged with state-of-the-art acoustic drivers. Features tactile solid brass bass and treble tuning dials and gold-plated RCA inputs.',
            json_encode([
                'Enclosure' => 'Handcrafted Solid Walnut Wood with Woven Grille',
                'Amplifiers' => 'Class D 60W Stereo Bi-Amplified',
                'Inputs' => 'Bluetooth 5.3, 3.5mm Aux, RCA Stereo Line-In',
                'Frequency Response' => '45Hz - 20,000Hz'
            ]),
            289.00, 329.00, 4.9, 115, 12, 'assets/images/products/p12.jpg', 'Exclusive', 0
        ],

        // Category 4: Wearables & Smart IoT (13-16)
        [
            13, 4, 'Chronos Titanium Smartwatch', 'chronos-titanium-smartwatch',
            'Grade 5 Titanium, Sapphire Crystal 1.43" AMOLED, Dual-Frequency GPS, ECG',
            'Built for extreme endurance and executive elegance. Comprehensive biometric monitoring including clinical-grade ECG, SpO2, HRV stress index, and skin temperature.',
            json_encode([
                'Case Material' => 'Aerospace Grade 5 Titanium + Sapphire Glass',
                'Display' => '1.43" Always-On AMOLED (466x466, 1500 nits)',
                'Sensors' => '8-Channel Photoplethysmography + ECG + Skin Temp',
                'Water Resistance' => '10 ATM (100m Dive Rated)',
                'Battery Life' => 'Up to 14 Days Smart Mode'
            ]),
            399.00, 449.00, 4.9, 195, 15, 'assets/images/products/p13.jpg', 'Editor Choice', 1
        ],
        [
            14, 4, 'FitVibe Pulse Band 3', 'fitvibe-pulse-band-3',
            'Continuous Heart Rate, Sleep Stage Analysis, 14-Day Battery, 50m Water Resist',
            'Ultra-lightweight fitness tracker that disappears on your wrist. Tracks 120+ workout modes, automatic activity detection, and guided breathing exercises.',
            json_encode([
                'Display' => '1.1" Vibrant AMOLED Screen',
                'Sensors' => 'Optical Pulse, Blood Oxygen, 3-Axis Accelerometer',
                'Battery' => '14 Days Typical Use, Magnetic Fast Charge',
                'Water Rating' => '5 ATM Swim-Proof'
            ]),
            129.00, 149.00, 4.6, 310, 40, 'assets/images/products/p14.jpg', 'Value', 0
        ],
        [
            15, 4, 'AuraRing Smart Health Tracker', 'auraring-smart-health-tracker',
            'Titanium Ceramic Sleep & Recovery Tracker Ring, Featherweight 4g, 7-Day Charge',
            'The most seamless health wearable. Worn comfortably on your finger to accurately capture pulse waves closer to arteries for unmatched sleep and recovery scores.',
            json_encode([
                'Material' => 'Titanium with PVD Diamond-Like Coating',
                'Weight' => '4 to 6 grams (depending on ring size)',
                'Sensors' => 'Infrared PPG, Body Temp, 3D Accelerometer',
                'Battery' => 'Up to 7 Days with Wireless Sizing Cradle'
            ]),
            299.00, 349.00, 4.8, 88, 18, 'assets/images/products/p15.jpg', 'Trending', 1
        ],
        [
            16, 4, 'VisionBeam AR Smart Glasses', 'visionbeam-ar-smart-glasses',
            'MicroLED Waveguide HUD Display, Spatial Open-Ear Audio, Voice AI Assistant',
            'Experience real-time navigation turn-by-turn overlays, live audio translation, and message previews projected right in your field of view without obstructing eyesight.',
            json_encode([
                'Optics' => 'Binocular MicroLED Waveguide (1080p equivalent)',
                'Audio' => 'Directional Stereo Micro-Speakers in Temples',
                'Weight' => '48g Everyday Frame Weight',
                'Connectivity' => 'Bluetooth 5.3, Wi-Fi 6, Dual MEMS Microphones'
            ]),
            599.00, 699.00, 4.7, 53, 9, 'assets/images/products/p16.jpg', 'Innovation', 0
        ],

        // Category 5: Gaming & Gear (17-20)
        [
            17, 5, 'Vortex Core Mechanical Keyboard', 'vortex-core-mechanical-keyboard',
            '75% Layout, Hot-Swappable Pre-Lubed Linear Switches, South-Facing RGB, Aluminum',
            'Engineered for typing perfection. Gasket-mounted CNC aluminum body with sound dampening PORON foam, PBT dye-sub keycaps, and multi-mode 2.4GHz / BT / USB-C connectivity.',
            json_encode([
                'Layout' => '75% Compact (82 Keys) with Rotary Volume Knob',
                'Switches' => 'Factory Lubed Silent Silver Linear (Hot-Swappable)',
                'Mounting' => 'Gasket Mount with 5-Layer Acoustic Foam Dampening',
                'Backlight' => 'Per-Key South-Facing 16.8M RGB with 22 Modes',
                'Connectivity' => 'Tri-Mode: 2.4GHz Wireless, Bluetooth 5.2, USB-C'
            ]),
            169.00, 199.00, 4.9, 240, 20, 'assets/images/products/p17.jpg', 'Best Seller', 1
        ],
        [
            18, 5, 'PrecisionPulse Wireless Mouse', 'precisionpulse-wireless-mouse',
            'Ultra-Lightweight 49g, 32,000 DPI Optical Sensor, 4000Hz Polling Rate',
            'Flawless precision with zero latency. Solid honeycomb-free shell weighs just 49 grams, outfitted with optical microswitches rated for 90 million clicks.',
            json_encode([
                'Weight' => '49 grams Ultra-Lightweight Ergonomic',
                'Sensor' => 'PixArt 3395 (32,000 DPI, 650 IPS, 50G Acceleration)',
                'Polling Rate' => 'Up to 4,000Hz Hyper-Polling',
                'Switches' => 'Optical Microswitches (0.2ms actuation, 90M clicks)',
                'Battery Life' => 'Up to 90 Hours Continuous Gaming'
            ]),
            119.00, 139.00, 4.8, 162, 28, 'assets/images/products/p18.jpg', 'Pro Gear', 0
        ],
        [
            19, 5, 'ImmersionVR Next Headset', 'immersionvr-next-headset',
            'Dual 4K QD-LCD Displays, Inside-Out Optical Tracking, Haptic Touch Controllers',
            'Step into hyper-realistic virtual worlds. Pancake optics provide edge-to-edge optical clarity with zero chromatic aberration, coupled with passthrough stereo cameras for mixed reality.',
            json_encode([
                'Resolution' => '4K per eye (3840x2160 per eye, 120Hz refresh)',
                'Optics' => 'Pancake Lenses with 110-degree Field of View',
                'Tracking' => '6-DoF Inside-Out Optical Tracking (No base stations)',
                'Audio' => 'Integrated Spatial Audio with deep bass resonance',
                'Controllers' => 'Ergonomic 6-DoF Controllers with TruTouch Haptics'
            ]),
            699.00, 799.00, 4.9, 94, 8, 'assets/images/products/p19.jpg', 'Flagship VR', 1
        ],
        [
            20, 5, 'OmniDock 14-in-1 Thunderbolt 4', 'omnidock-14-in-1-thunderbolt-4',
            'Dual 8K @ 60Hz or Triple 4K @ 144Hz, 100W Power Delivery, Anodized Metal',
            'The ultimate one-cable connectivity hub for your workstation. Delivers 40Gbps data bandwidth, Gigabit Ethernet, SD 4.0 card reader, and blazing 100W laptop power delivery.',
            json_encode([
                'Ports' => '3x TB4, 2x HDMI 2.1, 1x DP 1.4, 4x USB-A 3.2, 1x 2.5GbE, SD',
                'Power Delivery' => '100W Host Charging (180W DC Adapter included)',
                'Displays' => 'Dual 8K@60Hz or Triple 4K@144Hz Monitor Support',
                'Chassis' => 'Solid CNC Anodized Aluminum Heatsink'
            ]),
            229.00, 269.00, 4.8, 137, 18, 'assets/images/products/p20.jpg', 'Essential', 0
        ]
    ];

    $prodStmt = $pdo->prepare("INSERT OR IGNORE INTO products 
        (id, category_id, name, slug, short_desc, description, specs, price, old_price, rating, reviews_count, stock, image_url, badge, is_featured) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($products as $prod) {
        $prodStmt->execute($prod);
    }
}

// Helper query functions
function getCategories(): array {
    $pdo = getDb();
    return $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
}

function getProducts(?int $categoryId = null, ?string $search = null, ?string $sort = null): array {
    $pdo = getDb();
    $sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $params = [];

    if ($categoryId) {
        $sql .= " AND p.category_id = ?";
        $params[] = $categoryId;
    }

    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.short_desc LIKE ? OR p.description LIKE ?)";
        $wildcard = "%$search%";
        $params[] = $wildcard;
        $params[] = $wildcard;
        $params[] = $wildcard;
    }

    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'rating':
            $sql .= " ORDER BY p.rating DESC";
            break;
        default:
            $sql .= " ORDER BY p.id ASC";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductById(int $id): ?array {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    return $prod ?: null;
}

function getFeaturedProducts(int $limit = 8): array {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 ORDER BY p.id ASC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getRelatedProducts(int $categoryId, int $excludeId, int $limit = 4): array {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? ORDER BY p.rating DESC LIMIT ?");
    $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(2, $excludeId, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
