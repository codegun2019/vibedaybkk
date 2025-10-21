-- ================================================
-- Add Left Image Column for About Section
-- ================================================

-- เพิ่มคอลัมน์ left_image สำหรับรูปภาพด้านซ้าย
ALTER TABLE homepage_sections ADD COLUMN left_image VARCHAR(255) DEFAULT NULL;

-- อัปเดตข้อมูลที่มีอยู่
UPDATE homepage_sections SET left_image = NULL WHERE left_image IS NULL;

-- ตรวจสอบผลลัพธ์
SELECT 
    'Left image column added successfully!' as status,
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
