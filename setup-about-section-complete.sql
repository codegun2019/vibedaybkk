-- ================================================
-- Complete Setup for About Section
-- ================================================

-- เพิ่มคอลัมน์ background_type (ถ้ายังไม่มี)
ALTER TABLE homepage_sections ADD COLUMN background_type VARCHAR(20) DEFAULT 'color';

-- เพิ่มคอลัมน์ background_position (ถ้ายังไม่มี)
ALTER TABLE homepage_sections ADD COLUMN background_position VARCHAR(50) DEFAULT 'center';

-- เพิ่มคอลัมน์ background_size (ถ้ายังไม่มี)
ALTER TABLE homepage_sections ADD COLUMN background_size VARCHAR(50) DEFAULT 'cover';

-- เพิ่มคอลัมน์ background_repeat (ถ้ายังไม่มี)
ALTER TABLE homepage_sections ADD COLUMN background_repeat VARCHAR(50) DEFAULT 'no-repeat';

-- เพิ่มคอลัมน์ background_attachment (ถ้ายังไม่มี)
ALTER TABLE homepage_sections ADD COLUMN background_attachment VARCHAR(50) DEFAULT 'scroll';

-- เพิ่มคอลัมน์ left_image สำหรับรูปภาพด้านซ้าย
ALTER TABLE homepage_sections ADD COLUMN left_image VARCHAR(255) DEFAULT NULL;

-- อัปเดตข้อมูลที่มีอยู่ให้เป็นค่าเริ่มต้น
UPDATE homepage_sections SET background_type = 'color' WHERE background_type IS NULL;
UPDATE homepage_sections SET background_position = 'center' WHERE background_position IS NULL;
UPDATE homepage_sections SET background_size = 'cover' WHERE background_size IS NULL;
UPDATE homepage_sections SET background_repeat = 'no-repeat' WHERE background_repeat IS NULL;
UPDATE homepage_sections SET background_attachment = 'scroll' WHERE background_attachment IS NULL;
UPDATE homepage_sections SET left_image = NULL WHERE left_image IS NULL;

-- ตรวจสอบผลลัพธ์
SELECT 
    'About Section setup completed!' as status,
    COUNT(*) as total_sections
FROM homepage_sections;

-- แสดงข้อมูล About section
SELECT 
    id, 
    section_key, 
    left_image,
    background_type, 
    background_position, 
    background_size, 
    background_repeat, 
    background_attachment 
FROM homepage_sections 
WHERE section_key = 'about';

-- แสดงข้อมูล Hero section
SELECT 
    id, 
    section_key, 
    background_type, 
    background_position, 
    background_size, 
    background_repeat, 
    background_attachment 
FROM homepage_sections 
WHERE section_key = 'hero';
