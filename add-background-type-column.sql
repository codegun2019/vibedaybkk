-- เพิ่มคอลัมน์ background_type สำหรับเลือกใช้สีหรือรูปภาพ
-- ตรวจสอบว่าคอลัมน์มีอยู่แล้วหรือไม่ก่อนเพิ่ม
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'homepage_sections' 
     AND column_name = 'background_type' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "Column background_type already exists" as status;',
    'ALTER TABLE homepage_sections ADD COLUMN background_type VARCHAR(20) DEFAULT "color" COMMENT "ประเภทพื้นหลัง: color หรือ image";'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- อัปเดตข้อมูลที่มีอยู่ให้เป็น 'color'
UPDATE homepage_sections SET background_type = 'color' WHERE background_type IS NULL OR background_type = '';

-- เพิ่มคอลัมน์สำหรับ background settings
-- ตรวจสอบและเพิ่ม background_position
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'homepage_sections' 
     AND column_name = 'background_position' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "Column background_position already exists" as status;',
    'ALTER TABLE homepage_sections ADD COLUMN background_position VARCHAR(50) DEFAULT "center" COMMENT "ตำแหน่งพื้นหลัง";'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ตรวจสอบและเพิ่ม background_size
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'homepage_sections' 
     AND column_name = 'background_size' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "Column background_size already exists" as status;',
    'ALTER TABLE homepage_sections ADD COLUMN background_size VARCHAR(50) DEFAULT "cover" COMMENT "ขนาดพื้นหลัง";'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ตรวจสอบและเพิ่ม background_repeat
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'homepage_sections' 
     AND column_name = 'background_repeat' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "Column background_repeat already exists" as status;',
    'ALTER TABLE homepage_sections ADD COLUMN background_repeat VARCHAR(50) DEFAULT "no-repeat" COMMENT "การซ้ำพื้นหลัง";'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ตรวจสอบและเพิ่ม background_attachment
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'homepage_sections' 
     AND column_name = 'background_attachment' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "Column background_attachment already exists" as status;',
    'ALTER TABLE homepage_sections ADD COLUMN background_attachment VARCHAR(50) DEFAULT "scroll" COMMENT "การเลื่อนพื้นหลัง";'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ตรวจสอบผลลัพธ์
SELECT id, section_key, background_type, background_position, background_size, background_repeat, background_attachment 
FROM homepage_sections 
WHERE section_key = 'hero';

