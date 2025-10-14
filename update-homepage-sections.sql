-- ===================================
-- Homepage Sections Management System
-- ===================================

-- Create homepage_sections table
CREATE TABLE IF NOT EXISTS `homepage_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_key` varchar(50) NOT NULL COMMENT 'Unique key for section',
  `section_name` varchar(100) NOT NULL COMMENT 'Display name',
  `section_type` enum('hero','about','services','gallery','testimonials','cta','stats','features') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `background_color` varchar(50) DEFAULT NULL,
  `text_color` varchar(50) DEFAULT NULL,
  `settings` text DEFAULT NULL COMMENT 'JSON settings for section',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create homepage_gallery table for gallery images
CREATE TABLE IF NOT EXISTS `homepage_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  FOREIGN KEY (`section_id`) REFERENCES `homepage_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create homepage_features table for feature items
CREATE TABLE IF NOT EXISTS `homepage_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL COMMENT 'FontAwesome icon class',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  FOREIGN KEY (`section_id`) REFERENCES `homepage_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default homepage sections
INSERT INTO `homepage_sections` (`section_key`, `section_name`, `section_type`, `is_active`, `sort_order`, `title`, `subtitle`, `description`, `button_text`, `button_link`, `background_color`, `text_color`, `settings`) VALUES
('hero', 'Hero Section', 'hero', 1, 1, 
 'VIBEDAYBKK', 
 'Professional Model Agency in Bangkok', 
 'We provide top-tier professional models for fashion shows, events, and commercial projects. Our models are carefully selected and trained to deliver excellence.', 
 'ดูบริการของเรา', 
 'services.php', 
 '#1a1a1a', 
 '#ffffff',
 '{"overlay_opacity": "0.5", "text_align": "center", "animation": "fade-in"}'),

('about', 'About Section', 'about', 1, 2,
 'เกี่ยวกับเรา',
 'มืออาชีพ ประสบการณ์สูง',
 'VIBEDAYBKK เป็นเอเจนซี่โมเดลชั้นนำในกรุงเทพฯ ที่มีประสบการณ์กว่า 10 ปี ในการจัดหาโมเดลมืออาชีพสำหรับงานแฟชั่น อีเวนท์ และโฆษณา เรามีโมเดลที่ผ่านการคัดสรรและฝึกฝนมาอย่างดี พร้อมให้บริการในทุกประเภทงาน',
 'อ่านเพิ่มเติม',
 'about.php',
 '#ffffff',
 '#1a1a1a',
 '{"layout": "image-text", "image_position": "left"}'),

('services', 'Services Section', 'services', 1, 3,
 'บริการของเรา',
 'หมวดหมู่โมเดลที่หลากหลาย',
 'เรามีโมเดลมืออาชีพให้เลือกหลากหลายประเภท ตามความต้องการของคุณ',
 'ดูทั้งหมด',
 'services.php',
 '#f9fafb',
 '#1a1a1a',
 '{"columns": "3", "show_price": true}'),

('gallery', 'Gallery Section', 'gallery', 1, 4,
 'ผลงานของเรา',
 'ภาพจากงานที่ผ่านมา',
 'ชมผลงานและประสบการณ์ของโมเดลจากงานต่างๆ ที่เราได้ร่วมงานมา',
 'ดูทั้งหมด',
 'gallery.php',
 '#1a1a1a',
 '#ffffff',
 '{"columns": "4", "lightbox": true, "autoplay": false}'),

('testimonials', 'Testimonials Section', 'testimonials', 1, 5,
 'ความคิดเห็นจากลูกค้า',
 'สิ่งที่ลูกค้าพูดถึงเรา',
 'เราภูมิใจที่ได้รับความไว้วางใจจากลูกค้ามากมาย',
 NULL,
 NULL,
 '#ffffff',
 '#1a1a1a',
 '{"carousel": true, "autoplay": true, "interval": "5000"}'),

('stats', 'Statistics Section', 'stats', 1, 6,
 'ตัวเลขที่น่าประทับใจ',
 NULL,
 NULL,
 NULL,
 NULL,
 '#dc2626',
 '#ffffff',
 '{"columns": "4"}'),

('cta', 'Call to Action', 'cta', 1, 7,
 'พร้อมเริ่มโปรเจกต์กับเราแล้วหรือยัง?',
 'ติดต่อเราวันนี้เพื่อรับคำปรึกษาฟรี',
 'ทีมงานของเราพร้อมให้คำแนะนำและหาโมเดลที่เหมาะสมกับงานของคุณ',
 'ติดต่อเรา',
 'contact.php',
 '#dc2626',
 '#ffffff',
 '{"button_style": "large", "show_phone": true}');

-- Insert default gallery images
INSERT INTO `homepage_gallery` (`section_id`, `image_path`, `title`, `description`, `sort_order`, `is_active`) 
SELECT id, 'placeholder-gallery-1.jpg', 'Fashion Show 2024', 'Bangkok Fashion Week', 1, 1 FROM `homepage_sections` WHERE `section_key` = 'gallery'
UNION ALL
SELECT id, 'placeholder-gallery-2.jpg', 'Commercial Shoot', 'Product Photography', 2, 1 FROM `homepage_sections` WHERE `section_key` = 'gallery'
UNION ALL
SELECT id, 'placeholder-gallery-3.jpg', 'Event Coverage', 'Corporate Event', 3, 1 FROM `homepage_sections` WHERE `section_key` = 'gallery'
UNION ALL
SELECT id, 'placeholder-gallery-4.jpg', 'Magazine Editorial', 'Fashion Magazine', 4, 1 FROM `homepage_sections` WHERE `section_key` = 'gallery';

-- Insert default features/stats
INSERT INTO `homepage_features` (`section_id`, `icon`, `title`, `description`, `sort_order`, `is_active`)
SELECT id, 'fa-users', '500+', 'โมเดลมืออาชีพ', 1, 1 FROM `homepage_sections` WHERE `section_key` = 'stats'
UNION ALL
SELECT id, 'fa-briefcase', '1,000+', 'โปรเจกต์สำเร็จ', 2, 1 FROM `homepage_sections` WHERE `section_key` = 'stats'
UNION ALL
SELECT id, 'fa-award', '10+', 'ปีประสบการณ์', 3, 1 FROM `homepage_sections` WHERE `section_key` = 'stats'
UNION ALL
SELECT id, 'fa-smile', '100%', 'ความพึงพอใจ', 4, 1 FROM `homepage_sections` WHERE `section_key` = 'stats';

-- Insert default about features
INSERT INTO `homepage_features` (`section_id`, `icon`, `title`, `description`, `sort_order`, `is_active`)
SELECT id, 'fa-check-circle', 'โมเดลมืออาชีพ', 'คัดสรรโมเดลคุณภาพสูง ผ่านการฝึกฝนมาเป็นอย่างดี', 1, 1 FROM `homepage_sections` WHERE `section_key` = 'about'
UNION ALL
SELECT id, 'fa-clock', 'บริการรวดเร็ว', 'ตอบสนองความต้องการได้อย่างรวดเร็ว ทันเวลา', 2, 1 FROM `homepage_sections` WHERE `section_key` = 'about'
UNION ALL
SELECT id, 'fa-dollar-sign', 'ราคายุติธรรม', 'ราคาที่เหมาะสม คุ้มค่ากับคุณภาพที่ได้รับ', 3, 1 FROM `homepage_sections` WHERE `section_key` = 'about'
UNION ALL
SELECT id, 'fa-headset', 'ซัพพอร์ตตลอด 24/7', 'ทีมงานพร้อมให้คำปรึกษาและช่วยเหลือตลอดเวลา', 4, 1 FROM `homepage_sections` WHERE `section_key` = 'about';

-- Success message
SELECT 'Homepage sections tables created and populated successfully!' as message;

