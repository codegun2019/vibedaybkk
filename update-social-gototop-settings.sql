-- ===================================
-- Social Icons & Go to Top Settings
-- ระบบตั้งค่าไอคอนโซเชียลและปุ่ม Go to Top
-- ===================================

-- Add new settings for social icons and go to top
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
-- Social Media Icons
('social_facebook_url', 'https://facebook.com/vibedaybkk', 'text', 'Facebook URL'),
('social_facebook_icon', 'fa-facebook-f', 'text', 'Facebook Icon'),
('social_facebook_enabled', '1', 'boolean', 'Enable Facebook'),

('social_instagram_url', 'https://instagram.com/vibedaybkk', 'text', 'Instagram URL'),
('social_instagram_icon', 'fa-instagram', 'text', 'Instagram Icon'),
('social_instagram_enabled', '1', 'boolean', 'Enable Instagram'),

('social_twitter_url', 'https://twitter.com/vibedaybkk', 'text', 'Twitter URL'),
('social_twitter_icon', 'fa-twitter', 'text', 'Twitter Icon'),
('social_twitter_enabled', '1', 'boolean', 'Enable Twitter'),

('social_line_url', 'https://line.me/ti/p/@vibedaybkk', 'text', 'LINE URL'),
('social_line_icon', 'fa-line', 'text', 'LINE Icon'),
('social_line_enabled', '1', 'boolean', 'Enable LINE'),

('social_youtube_url', 'https://youtube.com/@vibedaybkk', 'text', 'YouTube URL'),
('social_youtube_icon', 'fa-youtube', 'text', 'YouTube Icon'),
('social_youtube_enabled', '0', 'boolean', 'Enable YouTube'),

('social_tiktok_url', 'https://tiktok.com/@vibedaybkk', 'text', 'TikTok URL'),
('social_tiktok_icon', 'fa-tiktok', 'text', 'TikTok Icon'),
('social_tiktok_enabled', '0', 'boolean', 'Enable TikTok'),

-- Go to Top Button Settings
('gototop_enabled', '1', 'boolean', 'Enable Go to Top Button'),
('gototop_icon', 'fa-arrow-up', 'text', 'Go to Top Icon'),
('gototop_bg_color', 'bg-red-primary', 'text', 'Go to Top Background Color'),
('gototop_text_color', 'text-white', 'text', 'Go to Top Text Color'),
('gototop_position', 'right', 'text', 'Go to Top Position (left/right)'),

-- Theme Colors
('theme_primary_color', '#DC2626', 'text', 'Primary Theme Color (Red)'),
('theme_secondary_color', '#1F2937', 'text', 'Secondary Theme Color (Dark Gray)'),
('theme_accent_color', '#F59E0B', 'text', 'Accent Theme Color (Orange)'),
('theme_background_color', '#0F172A', 'text', 'Background Color (Dark)'),
('theme_text_color', '#F3F4F6', 'text', 'Text Color (Light Gray)')

ON DUPLICATE KEY UPDATE 
    `setting_value` = VALUES(`setting_value`),
    `description` = VALUES(`description`);

-- Success message
SELECT 'Social icons and Go to Top settings created successfully!' as message,
       'Added 6 social media platforms' as step1,
       'Added Go to Top button settings' as step2,
       'Added theme color settings' as step3;



