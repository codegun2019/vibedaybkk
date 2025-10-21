-- ============================================
-- VIBEDAYBKK - Role-Based Access Control System
-- ระบบจัดการสิทธิ์การเข้าถึง
-- ============================================

-- 1. อัพเดทตาราง users - เพิ่ม role ใหม่
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('programmer', 'admin', 'editor', 'viewer') NOT NULL DEFAULT 'viewer';

-- 2. สร้างตาราง roles - กำหนดรายละเอียดของแต่ละ role
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_key` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text,
  `level` int(11) NOT NULL DEFAULT 0 COMMENT 'Level สูงสุด = สิทธิ์มากสุด',
  `color` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00 COMMENT 'ราคาสำหรับอัพเกรด',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_key` (`role_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. ใส่ข้อมูล roles (ข้ามถ้ามีอยู่แล้ว)
INSERT IGNORE INTO `roles` (`role_key`, `role_name`, `display_name`, `description`, `level`, `color`, `icon`, `price`, `is_active`) VALUES
('programmer', 'Programmer', 'โปรแกรมเมอร์', 'สิทธิ์สูงสุด - เข้าถึงทุกอย่างรวมถึงการตั้งค่าระบบ', 100, 'bg-purple-600', 'fa-code', 0.00, 1),
('admin', 'Admin', 'ผู้ดูแลระบบ', 'จัดการทุกอย่างยกเว้นการตั้งค่าระบบ', 80, 'bg-red-600', 'fa-user-shield', 0.00, 1),
('editor', 'Editor', 'บรรณาธิการ', 'สามารถเพิ่ม แก้ไข ลบข้อมูลได้', 50, 'bg-blue-600', 'fa-user-edit', 999.00, 1),
('viewer', 'Viewer', 'ผู้ดู', 'ดูข้อมูลได้อย่างเดียว ไม่สามารถแก้ไขได้', 10, 'bg-gray-600', 'fa-eye', 0.00, 1);

-- 4. สร้างตาราง permissions - กำหนดสิทธิ์แต่ละ feature
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_key` varchar(50) NOT NULL,
  `feature` varchar(100) NOT NULL COMMENT 'models, articles, bookings, etc.',
  `can_view` tinyint(1) DEFAULT 1,
  `can_create` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_export` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_feature` (`role_key`, `feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. ใส่ข้อมูล permissions (ข้ามถ้ามีอยู่แล้ว)

-- Programmer - สิทธิ์ทุกอย่าง
INSERT IGNORE INTO `permissions` (`role_key`, `feature`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`) VALUES
('programmer', 'models', 1, 1, 1, 1, 1),
('programmer', 'categories', 1, 1, 1, 1, 1),
('programmer', 'articles', 1, 1, 1, 1, 1),
('programmer', 'article_categories', 1, 1, 1, 1, 1),
('programmer', 'bookings', 1, 1, 1, 1, 1),
('programmer', 'contacts', 1, 1, 1, 1, 1),
('programmer', 'menus', 1, 1, 1, 1, 1),
('programmer', 'users', 1, 1, 1, 1, 1),
('programmer', 'gallery', 1, 1, 1, 1, 1),
('programmer', 'settings', 1, 1, 1, 1, 1),
('programmer', 'homepage', 1, 1, 1, 1, 1);

-- Admin - สิทธิ์เกือบทุกอย่าง ยกเว้น settings ระบบ
INSERT IGNORE INTO `permissions` (`role_key`, `feature`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`) VALUES
('admin', 'models', 1, 1, 1, 1, 1),
('admin', 'categories', 1, 1, 1, 1, 1),
('admin', 'articles', 1, 1, 1, 1, 1),
('admin', 'article_categories', 1, 1, 1, 1, 1),
('admin', 'bookings', 1, 1, 1, 1, 1),
('admin', 'contacts', 1, 1, 1, 1, 1),
('admin', 'menus', 1, 1, 1, 1, 1),
('admin', 'users', 1, 1, 1, 0, 1), -- ไม่สามารถลบ user ได้
('admin', 'gallery', 1, 1, 1, 1, 1),
('admin', 'settings', 1, 0, 1, 0, 0), -- ดูและแก้ไขได้ ลบไม่ได้
('admin', 'homepage', 1, 1, 1, 1, 1);

-- Editor - สามารถจัดการ content ได้
INSERT IGNORE INTO `permissions` (`role_key`, `feature`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`) VALUES
('editor', 'models', 1, 1, 1, 1, 1),
('editor', 'categories', 1, 1, 1, 0, 1), -- ลบไม่ได้
('editor', 'articles', 1, 1, 1, 1, 1),
('editor', 'article_categories', 1, 1, 1, 0, 1),
('editor', 'bookings', 1, 1, 1, 0, 1), -- ลบไม่ได้
('editor', 'contacts', 1, 0, 1, 0, 1), -- ตอบกลับได้ สร้างและลบไม่ได้
('editor', 'menus', 1, 0, 1, 0, 0), -- ดูและแก้ไขได้
('editor', 'users', 1, 0, 0, 0, 0), -- ดูได้อย่างเดียว (read-only)
('editor', 'gallery', 1, 1, 1, 1, 1),
('editor', 'settings', 1, 0, 0, 0, 0), -- ดูได้อย่างเดียว (read-only)
('editor', 'homepage', 1, 0, 1, 0, 0);

-- Viewer - ดูได้อย่างเดียว (Read-only)
INSERT IGNORE INTO `permissions` (`role_key`, `feature`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`) VALUES
('viewer', 'models', 1, 0, 0, 0, 1),
('viewer', 'categories', 1, 0, 0, 0, 1),
('viewer', 'articles', 1, 0, 0, 0, 1),
('viewer', 'article_categories', 1, 0, 0, 0, 1),
('viewer', 'bookings', 1, 0, 0, 0, 1),
('viewer', 'contacts', 1, 0, 0, 0, 1),
('viewer', 'menus', 1, 0, 0, 0, 0),
('viewer', 'users', 0, 0, 0, 0, 0), -- เข้าไม่ได้เลย
('viewer', 'gallery', 1, 0, 0, 0, 1),
('viewer', 'settings', 0, 0, 0, 0, 0), -- เข้าไม่ได้เลย
('viewer', 'homepage', 1, 0, 0, 0, 0);

-- 6. สร้างตาราง role_upgrades - บันทึกการซื้ออัพเกรด
CREATE TABLE IF NOT EXISTS `role_upgrades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `from_role` varchar(50) NOT NULL,
  `to_role` varchar(50) NOT NULL,
  `price_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_ref` varchar(100) DEFAULT NULL,
  `payment_status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
  `expires_at` datetime DEFAULT NULL COMMENT 'วันหมดอายุ (ถ้ามี)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. อัพเดท user ที่มีอยู่แล้ว
UPDATE `users` SET `role` = 'programmer' WHERE `username` = 'admin' OR `id` = 1 LIMIT 1;

-- 8. สร้างตาราง feature_locks - ล็อคฟีเจอร์ตาม role
CREATE TABLE IF NOT EXISTS `feature_locks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feature` varchar(100) NOT NULL,
  `min_role_level` int(11) NOT NULL DEFAULT 0 COMMENT 'Level ขั้นต่ำที่เข้าถึงได้',
  `is_premium` tinyint(1) DEFAULT 0 COMMENT 'ต้องจ่ายเงินหรือไม่',
  `price` decimal(10,2) DEFAULT 0.00,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feature` (`feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. ใส่ข้อมูล feature locks (ข้ามถ้ามีอยู่แล้ว)
INSERT IGNORE INTO `feature_locks` (`feature`, `min_role_level`, `is_premium`, `price`, `description`) VALUES
('models', 10, 0, 0.00, 'จัดการโมเดล - ฟรีสำหรับทุก role'),
('articles', 10, 0, 0.00, 'จัดการบทความ - ฟรีสำหรับทุก role'),
('gallery', 10, 0, 0.00, 'แกลเลอรี่ - ฟรีสำหรับทุก role'),
('bookings', 10, 0, 0.00, 'การจอง - ฟรีสำหรับทุก role'),
('contacts', 10, 0, 0.00, 'ข้อความติดต่อ - ฟรีสำหรับทุก role'),
('users', 80, 0, 0.00, 'จัดการผู้ใช้ - เฉพาะ Admin ขึ้นไป'),
('settings', 80, 0, 0.00, 'ตั้งค่าระบบ - เฉพาะ Admin ขึ้นไป'),
('advanced_analytics', 50, 1, 499.00, 'รายงานขั้นสูง - Premium Feature'),
('api_access', 50, 1, 999.00, 'API Access - Premium Feature'),
('white_label', 100, 1, 2999.00, 'White Label - Programmer Only');

-- 10. สร้าง view สำหรับดูข้อมูล role ง่ายๆ
CREATE OR REPLACE VIEW `user_roles_view` AS
SELECT 
    u.id,
    u.username,
    u.full_name,
    u.role,
    r.display_name as role_display_name,
    r.level as role_level,
    r.color as role_color,
    r.icon as role_icon,
    u.status,
    u.created_at
FROM users u
LEFT JOIN roles r ON u.role = r.role_key;

-- เสร็จสิ้น!
SELECT 'Role System Created Successfully!' as message;



