-- Setup All Homepage Sections with Default Values
-- ตั้งค่าข้อมูลเริ่มต้นสำหรับทุก Sections

-- Update Hero Section
UPDATE `homepage_sections` 
SET 
    `title` = 'VIBEDAYBKK',
    `subtitle` = 'บริการโมเดลและนางแบบมืออาชีพ',
    `description` = 'เราคือผู้เชี่ยวชาญด้านบริการโมเดลและนางแบบคุณภาพสูง พร้อมให้บริการสำหรับงานถ่ายภาพ งานแฟชั่น และงานอีเวนต์ต่างๆ ด้วยทีมงานมืออาชีพและโมเดลที่ผ่านการคัดสรรอย่างดี',
    `button_text` = 'จองบริการตอนนี้',
    `button_link` = '#contact',
    `background_color` = '#0a0a0a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.title_colors.vibe', '#DC2626',
        '$.title_colors.day', '#FFFFFF',
        '$.title_colors.bkk', '#DC2626',
        '$.section_id', 'home',
        '$.section_class', 'hero-gradient min-h-screen flex items-center pt-16',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true,
        '$.show_button', true
    )
WHERE `section_key` = 'hero';

-- Update About Section
UPDATE `homepage_sections` 
SET 
    `title` = 'เกี่ยวกับ VIBEDAYBKK',
    `subtitle` = 'แพลตฟอร์มบริการโมเดลและนางแบบที่ครบครัน',
    `description` = 'VIBEDAYBKK เป็นบริษัทชั้นนำด้านบริการโมเดลและนางแบบในกรุงเทพฯ เราให้บริการครบวงจรตั้งแต่การคัดสรรโมเดล การจัดการงาน ไปจนถึงการประสานงานในวันถ่ายทำ',
    `background_color` = '#1a1a1a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'about',
        '$.section_class', 'py-20 bg-dark-light',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true
    )
WHERE `section_key` = 'about';

-- Update Services Section
UPDATE `homepage_sections` 
SET 
    `title` = 'บริการของเรา',
    `subtitle` = 'เลือกบริการที่เหมาะสมกับความต้องการของคุณ',
    `description` = 'เรามีบริการหลากหลายสำหรับทุกความต้องการ',
    `background_color` = '#0a0a0a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'services',
        '$.section_class', 'py-20 bg-dark',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true
    )
WHERE `section_key` = 'services';

-- Update How to Book Section
UPDATE `homepage_sections` 
SET 
    `title` = 'วิธีการจองบริการ',
    `subtitle` = 'ง่าย รวดเร็ว และปลอดภัย',
    `description` = 'ขั้นตอนการจองบริการที่ง่ายและรวดเร็ว',
    `background_color` = '#1a1a1a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'how-to-book',
        '$.section_class', 'py-20 bg-dark-light',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true
    )
WHERE `section_key` = 'how_to_book';

-- Update Testimonials/Reviews Section
UPDATE `homepage_sections` 
SET 
    `title` = 'รีวิวจากลูกค้า',
    `subtitle` = 'ความพึงพอใจของลูกค้าคือสิ่งสำคัญที่สุดสำหรับเรา',
    `description` = 'ฟังเสียงจากลูกค้าที่ไว้วางใจเรา',
    `background_color` = '#0a0a0a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'reviews',
        '$.section_class', 'py-20 bg-dark',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true
    )
WHERE `section_key` = 'testimonials' OR `section_key` = 'reviews';

-- Update Contact Section
UPDATE `homepage_sections` 
SET 
    `title` = 'ติดต่อเรา',
    `subtitle` = 'พร้อมให้คำปรึกษาและรับจองบริการ',
    `description` = 'ติดต่อเราเพื่อรับคำปรึกษาและจองบริการ',
    `button_text` = 'ส่งข้อความ',
    `button_link` = '#contact',
    `background_color` = '#1a1a1a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'contact',
        '$.section_class', 'py-20 bg-dark-light',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true,
        '$.show_button', true
    )
WHERE `section_key` = 'contact';

-- Update Gallery Section
UPDATE `homepage_sections` 
SET 
    `title` = 'ผลงานของเรา',
    `subtitle` = 'ชมผลงานและแกลเลอรี่ของเรา',
    `description` = 'ภาพผลงานคุณภาพสูงจากโปรเจคที่ผ่านมา',
    `background_color` = '#0a0a0a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'gallery',
        '$.section_class', 'py-20 bg-dark',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true
    )
WHERE `section_key` = 'gallery';

-- Update Statistics Section
UPDATE `homepage_sections` 
SET 
    `title` = 'ตัวเลขที่น่าสนใจ',
    `subtitle` = 'ความสำเร็จของเราในตัวเลข',
    `description` = 'ผลงานและความสำเร็จที่ผ่านมา',
    `background_color` = '#1a1a1a',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'stats',
        '$.section_class', 'py-20 bg-dark-light',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true
    )
WHERE `section_key` = 'stats';

-- Update Call to Action Section
UPDATE `homepage_sections` 
SET 
    `title` = 'พร้อมเริ่มต้นแล้วหรือยัง?',
    `subtitle` = 'ติดต่อเราวันนี้เพื่อรับคำปรึกษาฟรี',
    `description` = 'ทีมงานมืออาชีพพร้อมให้บริการและให้คำปรึกษา',
    `button_text` = 'ติดต่อเราตอนนี้',
    `button_link` = '#contact',
    `background_color` = '',
    `text_color` = '#ffffff',
    `settings` = JSON_SET(
        COALESCE(JSON_EXTRACT(`settings`, '$'), '{}'),
        '$.section_id', 'cta',
        '$.section_class', 'py-20 bg-gradient-to-br from-red-600 to-red-700',
        '$.show_title', true,
        '$.show_subtitle', true,
        '$.show_description', true,
        '$.show_button', true
    )
WHERE `section_key` = 'cta';

-- Check results
SELECT `section_key`, `section_name`, `title`, `background_color`, `text_color` 
FROM `homepage_sections` 
WHERE `is_active` = 1 
ORDER BY `sort_order`;


