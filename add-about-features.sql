-- Add features column to homepage_sections table for About section
-- ไฟล์นี้ใช้สำหรับเพิ่มคอลัมน์ features ในตาราง homepage_sections

-- ตรวจสอบว่าคอลัมน์ features มีอยู่หรือไม่
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'homepage_sections' 
    AND COLUMN_NAME = 'features'
);

-- เพิ่มคอลัมน์ features ถ้ายังไม่มี
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE homepage_sections ADD COLUMN features TEXT NULL AFTER steps', 
    'SELECT "Column features already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- แสดงผลลัพธ์
SELECT 
    CASE 
        WHEN @column_exists = 0 THEN '✅ เพิ่มคอลัมน์ features สำเร็จ'
        ELSE 'ℹ️ คอลัมน์ features มีอยู่แล้ว'
    END as result;

-- ตรวจสอบโครงสร้างตาราง
DESCRIBE homepage_sections;
