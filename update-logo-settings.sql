-- Update Settings: เพิ่ม Logo และ Favicon
-- รันไฟล์นี้เพื่อเพิ่ม settings สำหรับจัดการ Logo และ Favicon

USE vibedaybkk;

-- เพิ่ม settings ใหม่ (ถ้ายังไม่มี)
INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES 
('logo_type', 'text', 'text', 'ประเภทโลโก้ (text, image)'),
('logo_text', 'VIBEDAYBKK', 'text', 'ข้อความโลโก้'),
('logo_image', '', 'text', 'รูปภาพโลโก้'),
('favicon', '', 'text', 'Favicon'),
('facebook_url', '', 'text', 'Facebook URL'),
('instagram_url', '', 'text', 'Instagram URL'),
('twitter_url', '', 'text', 'Twitter/X URL');

-- แสดงผลลัพธ์
SELECT 'Settings updated successfully!' as status;
SELECT * FROM settings WHERE setting_key IN ('logo_type', 'logo_text', 'logo_image', 'favicon', 'facebook_url', 'instagram_url', 'twitter_url');

