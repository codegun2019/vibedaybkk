-- ฐานข้อมูล VIBEDAYBKK Management System
-- สร้างวันที่: 2025-10-14

CREATE DATABASE IF NOT EXISTS vibedaybkk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vibedaybkk;

-- ตาราง users (ผู้ใช้งานระบบ)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูล admin เริ่มต้น (username: admin, password: admin123)
INSERT INTO users (username, password, full_name, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin@vibedaybkk.com', 'admin');

-- ตาราง categories (หมวดหมู่บริการ)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(50),
    gender ENUM('female', 'male', 'all') DEFAULT 'all',
    price_min DECIMAL(10,2),
    price_max DECIMAL(10,2),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลหมวดหมู่เริ่มต้น
INSERT INTO categories (code, name, name_en, description, icon, color, gender, price_min, price_max, sort_order) VALUES 
('female-fashion', 'โมเดลแฟชั่นหญิง', 'Fashion Models', 'สำหรับงานถ่ายแฟชั่น แคตตาล็อก และงานรันเวย์', 'fa-female', 'from-pink-500 to-red-primary', 'female', 3000, 5000, 1),
('female-photography', 'โมเดลถ่ายภาพหญิง', 'Photography Models', 'สำหรับงานถ่ายภาพโฆษณา แคตตาล็อกสินค้า', 'fa-camera', 'from-purple-500 to-pink-500', 'female', 2500, 4000, 2),
('female-event', 'นางแบบอีเวนต์', 'Event Models', 'สำหรับงานแสดงสินค้า งานมอเตอร์โชว์', 'fa-star', 'from-red-primary to-red-light', 'female', 2000, 3500, 3),
('male-fashion', 'โมเดลแฟชั่นชาย', 'Male Fashion Models', 'สำหรับงานถ่ายแฟชั่นผู้ชาย แคตตาล็อก', 'fa-male', 'from-blue-500 to-indigo-600', 'male', 3500, 6000, 4),
('male-fitness', 'โมเดลฟิตเนส', 'Fitness Models', 'สำหรับงานถ่ายโฆษณาฟิตเนส อาหารเสริม', 'fa-dumbbell', 'from-green-500 to-teal-600', 'male', 3000, 5000, 5),
('male-business', 'โมเดลธุรกิจ', 'Business Models', 'สำหรับงานถ่ายโฆษณาธุรกิจ คอร์ปอเรต', 'fa-briefcase', 'from-orange-500 to-red-500', 'male', 2500, 4500, 6);

-- ตาราง models (ข้อมูลโมเดล)
CREATE TABLE IF NOT EXISTS models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    description TEXT,
    price_min DECIMAL(10,2),
    price_max DECIMAL(10,2),
    height INT COMMENT 'ส่วนสูงในหน่วย cm',
    weight INT COMMENT 'น้ำหนักในหน่วย kg',
    bust INT COMMENT 'รอบอก (สำหรับหญิง)',
    waist INT COMMENT 'รอบเอว',
    hips INT COMMENT 'รอบสะโพก',
    experience_years INT DEFAULT 0,
    age INT,
    skin_tone VARCHAR(50),
    hair_color VARCHAR(50),
    eye_color VARCHAR(50),
    languages TEXT COMMENT 'ภาษาที่พูดได้',
    skills TEXT COMMENT 'ความสามารถพิเศษ',
    featured TINYINT(1) DEFAULT 0,
    status ENUM('available', 'busy', 'inactive') DEFAULT 'available',
    view_count INT DEFAULT 0,
    booking_count INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง model_images (รูปภาพโมเดล)
CREATE TABLE IF NOT EXISTS model_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('profile', 'portfolio', 'cover') DEFAULT 'portfolio',
    title VARCHAR(255),
    alt_text VARCHAR(255),
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง model_requirements (คุณสมบัติที่ต้องการ)
CREATE TABLE IF NOT EXISTS model_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    requirement TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลคุณสมบัติเริ่มต้น
INSERT INTO model_requirements (category_id, requirement, sort_order) VALUES 
(1, 'ส่วนสูง 165-175 cm', 1),
(1, 'รูปร่างสมส่วนและสวยงาม', 2),
(1, 'มีประสบการณ์งานแฟชั่น', 3),
(1, 'เดินรันเวย์ได้อย่างมืออาชีพ', 4),
(1, 'มีพอร์ตโฟลิโอครบถ้วน', 5);

-- ตาราง articles (บทความ)
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category VARCHAR(100),
    author_id INT,
    read_time INT DEFAULT 5,
    view_count INT DEFAULT 0,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง menus (เมนูนำทาง)
CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    target ENUM('_self', '_blank') DEFAULT '_self',
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menus(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลเมนูเริ่มต้น
INSERT INTO menus (title, url, icon, sort_order) VALUES 
('หน้าแรก', 'index.html', 'fa-home', 1),
('เกี่ยวกับเรา', 'index.html#about', 'fa-info-circle', 2),
('บริการ', 'services.html', 'fa-briefcase', 3),
('บทความ', 'articles.html', 'fa-newspaper', 4),
('ติดต่อ', 'index.html#contact', 'fa-envelope', 5);

-- ตาราง contacts (ข้อมูลติดต่อ)
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service_type VARCHAR(100),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    replied_at DATETIME NULL,
    replied_by INT NULL,
    reply_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง settings (ตั้งค่าระบบ)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลตั้งค่าเริ่มต้น
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES 
('site_name', 'VIBEDAYBKK', 'text', 'ชื่อเว็บไซต์'),
('site_email', 'info@vibedaybkk.com', 'text', 'อีเมลติดต่อ'),
('site_phone', '02-123-4567', 'text', 'เบอร์โทรศัพท์'),
('site_line', '@vibedaybkk', 'text', 'LINE ID'),
('site_address', '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110', 'text', 'ที่อยู่'),
('booking_advance_days', '3', 'number', 'จองล่วงหน้าอย่างน้อย (วัน)'),
('items_per_page', '12', 'number', 'จำนวนรายการต่อหน้า'),
('enable_registration', 'false', 'boolean', 'เปิดให้สมัครสมาชิก');

-- ตาราง bookings (การจองโมเดล)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_days INT DEFAULT 1,
    service_type VARCHAR(100),
    location TEXT,
    message TEXT,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'deposit', 'paid', 'refunded') DEFAULT 'unpaid',
    confirmed_at DATETIME NULL,
    confirmed_by INT NULL,
    cancelled_at DATETIME NULL,
    cancel_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE,
    FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง activity_logs (บันทึกการใช้งาน)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- สร้าง indexes เพื่อเพิ่มประสิทธิภาพ
CREATE INDEX idx_models_category ON models(category_id);
CREATE INDEX idx_models_status ON models(status);
CREATE INDEX idx_models_featured ON models(featured);
CREATE INDEX idx_articles_status ON articles(status);
CREATE INDEX idx_articles_slug ON articles(slug);
CREATE INDEX idx_contacts_status ON contacts(status);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_model ON bookings(model_id);
CREATE INDEX idx_bookings_date ON bookings(booking_date);

-- สร้าง views สำหรับรายงาน
CREATE OR REPLACE VIEW v_model_statistics AS
SELECT 
    m.id,
    m.code,
    m.name,
    c.name as category_name,
    m.status,
    m.view_count,
    m.booking_count,
    m.rating,
    COUNT(DISTINCT mi.id) as image_count,
    COUNT(DISTINCT b.id) as total_bookings,
    SUM(CASE WHEN b.status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
    COALESCE(SUM(CASE WHEN b.status = 'completed' THEN b.total_price ELSE 0 END), 0) as total_revenue
FROM models m
LEFT JOIN categories c ON m.category_id = c.id
LEFT JOIN model_images mi ON m.id = mi.model_id
LEFT JOIN bookings b ON m.id = b.model_id
GROUP BY m.id;

-- สร้าง stored procedures
DELIMITER //

-- Procedure: เพิ่มโมเดลพร้อมข้อมูลครบถ้วน
CREATE PROCEDURE sp_create_model(
    IN p_category_id INT,
    IN p_code VARCHAR(50),
    IN p_name VARCHAR(100),
    IN p_description TEXT,
    IN p_price_min DECIMAL(10,2),
    IN p_price_max DECIMAL(10,2),
    IN p_height INT,
    IN p_experience_years INT
)
BEGIN
    INSERT INTO models (category_id, code, name, description, price_min, price_max, height, experience_years)
    VALUES (p_category_id, p_code, p_name, p_description, p_price_min, p_price_max, p_height, p_experience_years);
    
    SELECT LAST_INSERT_ID() as model_id;
END //

-- Procedure: อัพเดทสถิติโมเดล
CREATE PROCEDURE sp_update_model_stats(IN p_model_id INT)
BEGIN
    UPDATE models m
    SET 
        m.booking_count = (SELECT COUNT(*) FROM bookings WHERE model_id = p_model_id AND status = 'completed'),
        m.rating = (SELECT COALESCE(AVG(rating), 0) FROM bookings WHERE model_id = p_model_id AND rating IS NOT NULL)
    WHERE m.id = p_model_id;
END //

DELIMITER ;

-- สิ้นสุดการสร้างฐานข้อมูล

