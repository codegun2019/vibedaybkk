-- ============================================
-- Enable Editor Full Access to Homepage & Content
-- เปิดให้ Editor จัดการหน้าแรกและเนื้อหาได้เต็มรูปแบบ
-- ============================================

-- Update Editor permissions - เปิดทุกอย่างสำหรับ Homepage
UPDATE `permissions` 
SET can_view = 1, can_create = 1, can_edit = 1, can_delete = 1, can_export = 1
WHERE role_key = 'editor' AND feature = 'homepage';

-- Update Editor permissions - เปิดทุกอย่างสำหรับเนื้อหา
UPDATE `permissions` 
SET can_view = 1, can_create = 1, can_edit = 1, can_delete = 1, can_export = 1
WHERE role_key = 'editor' AND feature IN ('models', 'categories', 'articles', 'article_categories', 'gallery', 'bookings', 'contacts', 'menus');

-- Update Editor permissions - เปิด view สำหรับ Users (ไม่ให้แก้ไข)
UPDATE `permissions` 
SET can_view = 1, can_create = 0, can_edit = 0, can_delete = 0, can_export = 0
WHERE role_key = 'editor' AND feature = 'users';

-- Update Editor permissions - เปิด view สำหรับ Settings (ไม่ให้แก้ไข)
UPDATE `permissions` 
SET can_view = 1, can_create = 0, can_edit = 0, can_delete = 0, can_export = 0
WHERE role_key = 'editor' AND feature = 'settings';

-- ถ้ายังไม่มี record ให้สร้างใหม่
INSERT IGNORE INTO `permissions` (`role_key`, `feature`, `can_view`, `can_create`, `can_edit`, `can_delete`, `can_export`) VALUES
('editor', 'homepage', 1, 1, 1, 1, 1),
('editor', 'models', 1, 1, 1, 1, 1),
('editor', 'categories', 1, 1, 1, 1, 1),
('editor', 'articles', 1, 1, 1, 1, 1),
('editor', 'article_categories', 1, 1, 1, 1, 1),
('editor', 'gallery', 1, 1, 1, 1, 1),
('editor', 'bookings', 1, 1, 1, 1, 1),
('editor', 'contacts', 1, 1, 1, 1, 1),
('editor', 'menus', 1, 1, 1, 1, 1),
('editor', 'users', 1, 0, 0, 0, 0),
('editor', 'settings', 1, 0, 0, 0, 0);

SELECT 'Editor now has full access to Homepage and all content features!' as message;



