-- ===================================
-- Sample Data for VIBEDAYBKK
-- ข้อมูลตัวอย่าง 10 รายการต่อ module
-- ===================================

-- Insert 10 Models (skip if exists)
INSERT IGNORE INTO `models` (`category_id`, `code`, `name`, `name_en`, `description`, `price_min`, `price_max`, `height`, `weight`, `age`, `bust`, `waist`, `hips`, `experience_years`, `skin_tone`, `hair_color`, `eye_color`, `languages`, `skills`, `featured`, `status`, `sort_order`) VALUES
(1, 'FM001', 'สุภาพร สวยงาม', 'Supaporn Suayngam', 'โมเดลมืออาชีพ มีประสบการณ์ในงานแฟชั่นโชว์และถ่ายแบบ', 3000, 5000, 170, 48, 25, 34, 24, 36, 5, 'ขาว', 'ดำ', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, MC', 1, 'available', 1),
(1, 'FM002', 'วรรณา ใจดี', 'Wanna Jaidee', 'โมเดลรุ่นใหม่ มีความสดใส เหมาะกับงานอีเวนท์', 2500, 4000, 168, 47, 23, 33, 23, 35, 3, 'ขาวเหลือง', 'น้ำตาล', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ', 1, 'available', 2),
(1, 'FM003', 'ปิยะนุช มั่นคง', 'Piyanut Munkong', 'โมเดลที่มีความเป็นมืออาชีพสูง ทำงานได้หลากหลาย', 3500, 6000, 172, 50, 27, 35, 25, 37, 7, 'ขาว', 'ดำ', 'ดำ', 'ไทย, อังกฤษ, จีน', 'เดินรันเวย์, ถ่ายแบบ, MC, พรีเซ็นเตอร์', 1, 'available', 3),
(2, 'MM001', 'ธนพล หล่อเหลา', 'Thanapol Lolao', 'โมเดลชายมืออาชีพ รูปร่างสมส่วน', 3000, 5000, 180, 70, 28, NULL, NULL, NULL, 6, 'ขาวเหลือง', 'ดำ', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, แอคติ้ง', 1, 'available', 4),
(2, 'MM002', 'สมชาย ใจกล้า', 'Somchai Jaikla', 'โมเดลชายที่มีบุคลิกเด่น เหมาะกับงานโฆษณา', 2800, 4500, 178, 68, 26, NULL, NULL, NULL, 4, 'ขาว', 'น้ำตาล', 'น้ำตาล', 'ไทย, อังกฤษ', 'ถ่ายแบบ, พรีเซ็นเตอร์', 0, 'available', 5),
(1, 'FM004', 'ชญานิศ สง่างาม', 'Chayanit Sangngam', 'โมเดลที่มีความสง่างาม เหมาะกับงานแฟชั่นระดับสูง', 4000, 7000, 175, 52, 29, 36, 26, 38, 8, 'ขาว', 'ดำ', 'ดำ', 'ไทย, อังกฤษ, ฝรั่งเศส', 'เดินรันเวย์, ถ่ายแบบ, MC', 1, 'available', 6),
(1, 'FM005', 'นภัสวรรณ รุ่งเรือง', 'Napatsawan Rungruang', 'โมเดลหน้าใหม่ มีความสดใสและมีพลัง', 2000, 3500, 165, 45, 22, 32, 23, 34, 2, 'ขาวเหลือง', 'น้ำตาล', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ', 0, 'available', 7),
(2, 'MM003', 'ภูมิพัฒน์ เจริญสุข', 'Phumipat Charoensuk', 'โมเดลชายที่มีความมั่นใจ เหมาะกับงานแฟชั่น', 3200, 5500, 182, 72, 30, NULL, NULL, NULL, 7, 'ขาว', 'ดำ', 'ดำ', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, แอคติ้ง', 1, 'available', 8),
(1, 'FM006', 'กมลวรรณ ศรีสุข', 'Kamonwan Srisuk', 'โมเดลที่มีความเป็นธรรมชาติ เหมาะกับงานถ่ายแบบ', 2500, 4000, 167, 46, 24, 33, 24, 35, 4, 'ขาว', 'น้ำตาลอ่อน', 'น้ำตาล', 'ไทย, อังกฤษ', 'ถ่ายแบบ, พรีเซ็นเตอร์', 0, 'available', 9),
(2, 'MM004', 'วีรภัทร แข็งแรง', 'Weerapat Kaengraeng', 'โมเดลชายที่มีรูปร่างแข็งแรง เหมาะกับงานสปอร์ต', 2800, 4500, 179, 75, 27, NULL, NULL, NULL, 5, 'ขาวเหลือง', 'ดำ', 'น้ำตาล', 'ไทย, อังกฤษ', 'เดินรันเวย์, ถ่ายแบบ, ฟิตเนส', 0, 'available', 10);

-- Insert 10 Articles (skip if slug exists)
INSERT IGNORE INTO `articles` (`title`, `slug`, `excerpt`, `content`, `category`, `author_id`, `read_time`, `status`, `published_at`) VALUES
('เทรนด์แฟชั่น 2024 ที่ต้องจับตามอง', 'fashion-trends-2024', 'รวมเทรนด์แฟชั่นที่กำลังมาแรงในปี 2024', '<p>เทรนด์แฟชั่นในปี 2024 มีความหลากหลายและน่าสนใจมาก ตั้งแต่สีสันสดใส ไปจนถึงการตัดเย็บที่เน้นความเรียบง่าย</p><p>ไฮไลท์สำคัญ ได้แก่ การใช้วัสดุที่เป็นมิตรกับสิ่งแวดล้อม และการผสมผสานสไตล์วินเทจเข้ากับความทันสมัย</p>', 'Fashion', 1, 5, 'published', NOW()),
('10 ทริคการถ่ายภาพแฟชั่นให้สวยปัง', '10-fashion-photography-tips', 'เทคนิคการถ่ายภาพแฟชั่นสำหรับมือใหม่', '<p>การถ่ายภาพแฟชั่นไม่ใช่เรื่องยาก หากคุณรู้เทคนิคพื้นฐาน</p><p>1. เลือกแสงที่เหมาะสม<br>2. ใช้มุมกล้องที่น่าสนใจ<br>3. ดูแลรายละเอียดของชุด<br>4. สื่อสารกับโมเดลให้ชัดเจน<br>5. ใช้โปรแกรมแต่งภาพอย่างเหมาะสม</p>', 'Photography', 1, 7, 'published', NOW()),
('วิธีเลือกโมเดลที่เหมาะกับงาน', 'how-to-choose-right-model', 'คำแนะนำในการเลือกโมเดลสำหรับงานต่างๆ', '<p>การเลือกโมเดลที่เหมาะสมเป็นสิ่งสำคัญต่อความสำเร็จของงาน</p><p>พิจารณาจาก: ประเภทงาน, งบประมาณ, ประสบการณ์, บุคลิกภาพ, และความเหมาะสมกับแบรนด์</p>', 'Tips', 1, 6, 'published', NOW()),
('Behind the Scenes: Bangkok Fashion Week', 'behind-scenes-bangkok-fashion-week', 'เบื้องหลังงาน Bangkok Fashion Week 2024', '<p>พาไปดูเบื้องหลังงานแฟชั่นที่ยิ่งใหญ่ที่สุดของปี</p><p>ตั้งแต่การเตรียมตัว การซ้อม ไปจนถึงการแสดงบนรันเวย์ ทุกขั้นตอนต้องใช้ความพยายามและความมืออาชีพสูง</p>', 'Events', 1, 8, 'published', NOW()),
('การดูแลผิวพรรณสำหรับโมเดล', 'skincare-for-models', 'เคล็ดลับการดูแลผิวให้สวยใสเหมือนโมเดล', '<p>ผิวพรรณที่ดีเป็นทุนสำคัญของโมเดล</p><p>แนะนำ: ดื่มน้ำเยอะๆ, นอนหลับพักผ่อนเพียงพอ, ใช้ครีมกันแดด, ทำความสะอาดผิวอย่างสม่ำเสมอ</p>', 'Beauty', 1, 5, 'published', NOW()),
('เทคนิคการเดินแบบสำหรับมือใหม่', 'runway-walking-techniques', 'เรียนรู้การเดินรันเวย์แบบมืออาชีพ', '<p>การเดินรันเวย์เป็นศิลปะที่ต้องฝึกฝน</p><p>เทคนิคพื้นฐาน: ยืนตัวตรง, เดินมั่นใจ, สบตากล้อง, หมุนตัวอย่างสง่างาม, รักษาจังหวะการเดิน</p>', 'Tutorial', 1, 6, 'published', NOW()),
('แฟชั่นโชว์ที่น่าจับตามองในปีนี้', 'fashion-shows-2024', 'รวมแฟชั่นโชว์สำคัญในปี 2024', '<p>ปีนี้มีแฟชั่นโชว์ใหญ่ๆ มากมาย</p><p>ไม่ว่าจะเป็น Bangkok Fashion Week, Thailand Fashion Week, และอีกหลายงานที่น่าสนใจ</p>', 'Events', 1, 4, 'published', NOW()),
('การเตรียมตัวก่อนถ่ายแบบ', 'prepare-before-photoshoot', 'สิ่งที่ต้องเตรียมก่อนไปถ่ายแบบ', '<p>การเตรียมตัวที่ดีจะทำให้งานถ่ายแบบราบรื่น</p><p>เช็คลิสต์: ดูแลผิวพรรณ, นอนหลับพักผ่อน, เตรียมชุดสำรอง, ทำความสะอาดเล็บ, เตรียมอุปกรณ์ส่วนตัว</p>', 'Tips', 1, 5, 'published', NOW()),
('ประวัติความเป็นมาของ VIBEDAYBKK', 'about-vibedaybkk', 'เรื่องราวของเราตั้งแต่วันแรก', '<p>VIBEDAYBKK ก่อตั้งขึ้นในปี 2014 ด้วยความมุ่งมั่นที่จะเป็นเอเจนซี่โมเดลชั้นนำ</p><p>เราเติบโตมาพร้อมกับโมเดลมากกว่า 500 คน และทำงานกับแบรนด์ชั้นนำมากมาย</p>', 'About', 1, 4, 'published', NOW()),
('แนวโน้มการตลาดด้วยโมเดล', 'model-marketing-trends', 'การใช้โมเดลในการตลาดยุคใหม่', '<p>การตลาดด้วยโมเดลในยุคดิจิทัลมีความสำคัญมากขึ้น</p><p>ไม่ว่าจะเป็น Social Media Marketing, Influencer Marketing, หรือ Content Marketing ล้วนต้องใช้โมเดลที่เหมาะสม</p>', 'Marketing', 1, 7, 'published', NOW());

-- Insert 10 Menus (skip if exists)
INSERT IGNORE INTO `menus` (`parent_id`, `title`, `url`, `icon`, `target`, `sort_order`, `status`) VALUES
(NULL, 'หน้าแรก', 'index.php', 'fa-home', '_self', 1, 'active'),
(NULL, 'เกี่ยวกับเรา', 'about.php', 'fa-info-circle', '_self', 2, 'active'),
(NULL, 'บริการ', 'services.php', 'fa-briefcase', '_self', 3, 'active'),
(NULL, 'โมเดล', 'models.php', 'fa-users', '_self', 4, 'active'),
(NULL, 'บทความ', 'articles.php', 'fa-newspaper', '_self', 5, 'active'),
(NULL, 'แกลเลอรี่', 'gallery.php', 'fa-images', '_self', 6, 'active'),
(NULL, 'ติดต่อเรา', 'contact.php', 'fa-envelope', '_self', 7, 'active'),
(3, 'โมเดลหญิง', 'services-detail.php?category=female-fashion', 'fa-female', '_self', 1, 'active'),
(3, 'โมเดลชาย', 'services-detail.php?category=male-fashion', 'fa-male', '_self', 2, 'active'),
(3, 'โมเดลเด็ก', 'services-detail.php?category=kids', 'fa-child', '_self', 3, 'active');

-- Insert 10 Contacts
INSERT INTO `contacts` (`name`, `email`, `phone`, `service_type`, `message`, `status`) VALUES
('สมชาย ใจดี', 'somchai@email.com', '081-234-5678', 'Fashion Show', 'สนใจจองโมเดลสำหรับงานแฟชั่นโชว์ วันที่ 15 มกราคม 2024', 'new'),
('วิภา สวยงาม', 'wipa@email.com', '082-345-6789', 'Photo Shoot', 'ต้องการโมเดลหญิง 2 คน สำหรับถ่ายแบบโฆษณา', 'new'),
('ธนพล รุ่งเรือง', 'thanapol@email.com', '083-456-7890', 'Event', 'สอบถามราคาโมเดลสำหรับงานเปิดตัวสินค้า', 'read'),
('ปิยะนุช มั่นคง', 'piyanut@email.com', '084-567-8901', 'Commercial', 'ต้องการโมเดลชายสำหรับถ่ายโฆษณารถยนต์', 'read'),
('นภัสวรรณ เจริญ', 'napatsawan@email.com', '085-678-9012', 'Fashion Show', 'สนใจจองโมเดล 5 คน สำหรับงาน Fashion Week', 'replied'),
('อรุณ สว่างจิต', 'arun@email.com', '086-789-0123', 'Photo Shoot', 'ต้องการโมเดลสำหรับถ่ายแบบนิตยสาร', 'replied'),
('ชญานิศ ใจงาม', 'chayanit@email.com', '087-890-1234', 'Event', 'สอบถามข้อมูลการจองโมเดลล่วงหน้า', 'closed'),
('ภูมิพัฒน์ สุขใจ', 'phumipat@email.com', '088-901-2345', 'Commercial', 'ต้องการโมเดลสำหรับถ่ายโฆษณาอาหาร', 'new'),
('กมลวรรณ ดีมาก', 'kamonwan@email.com', '089-012-3456', 'Fashion Show', 'สนใจจองโมเดลสำหรับงานเปิดตัวแบรนด์', 'new'),
('วีรภัทร สมบูรณ์', 'weerapat@email.com', '090-123-4567', 'Photo Shoot', 'ต้องการโมเดลชายและหญิงอย่างละ 2 คน', 'read');

-- Insert 10 Bookings
INSERT INTO `bookings` (`model_id`, `customer_name`, `customer_email`, `customer_phone`, `service_type`, `booking_date`, `location`, `booking_days`, `total_price`, `message`, `status`) VALUES
(1, 'บริษัท แฟชั่น จำกัด', 'fashion@company.com', '02-123-4567', 'Fashion Show', '2024-11-15', 'ศูนย์การค้าสยามพารากอน', 1, 5000, 'ต้องการโมเดลมาซ้อมล่วงหน้า 1 วัน', 'confirmed'),
(2, 'บริษัท โฆษณา จำกัด', 'ads@company.com', '02-234-5678', 'Commercial Shoot', '2024-11-20', 'สตูดิโอ ถ่ายภาพ กรุงเทพ', 2, 8000, 'ถ่ายโฆษณาเครื่องสำอาง', 'confirmed'),
(3, 'นิตยสาร แฟชั่น', 'magazine@fashion.com', '02-345-6789', 'Editorial Shoot', '2024-11-25', 'หาดชะอำ', 3, 18000, 'ถ่ายแบบริมทะเล', 'pending'),
(4, 'บริษัท อีเวนท์ จำกัด', 'event@company.com', '02-456-7890', 'Product Launch', '2024-12-01', 'โรงแรม 5 ดาว', 1, 5000, 'เป็น MC และโมเดลประจำบูธ', 'pending'),
(5, 'แบรนด์ กีฬา', 'sports@brand.com', '02-567-8901', 'Commercial Shoot', '2024-12-05', 'สนามกีฬา', 1, 4500, 'ถ่ายโฆษณาชุดกีฬา', 'confirmed'),
(6, 'บริษัท ท่องเที่ยว', 'travel@company.com', '02-678-9012', 'Promotional', '2024-12-10', 'ภูเก็ต', 4, 24000, 'ถ่ายโปรโมทรีสอร์ท', 'completed'),
(7, 'แบรนด์ เครื่องสำอาง', 'beauty@brand.com', '02-789-0123', 'Commercial Shoot', '2024-12-15', 'สตูดิโอ', 1, 3500, 'ถ่ายโฆษณาลิปสติก', 'completed'),
(8, 'บริษัท รถยนต์', 'car@company.com', '02-890-1234', 'Commercial Shoot', '2024-12-20', 'โชว์รูม', 2, 11000, 'ถ่ายโฆษณารถยนต์รุ่นใหม่', 'pending'),
(9, 'นิตยสาร ไลฟ์สไตล์', 'lifestyle@magazine.com', '02-901-2345', 'Editorial Shoot', '2024-12-25', 'กรุงเทพ', 1, 4000, 'ถ่ายแบบไลฟ์สไตล์', 'confirmed'),
(10, 'แบรนด์ แฟชั่น', 'fashion@brand.com', '02-012-3456', 'Fashion Show', '2024-12-30', 'ศูนย์ประชุม', 1, 5500, 'งานเปิดตัวคอลเลคชั่นใหม่', 'pending');

-- Insert 10 Users (skip if username exists)
INSERT IGNORE INTO `users` (`username`, `password`, `full_name`, `email`, `role`, `status`) VALUES
('editor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมชาย บรรณาธิการ', 'editor1@vibedaybkk.com', 'editor', 'active'),
('editor2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วิภา จัดการ', 'editor2@vibedaybkk.com', 'editor', 'active'),
('editor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ธนพล ดูแล', 'editor3@vibedaybkk.com', 'editor', 'active'),
('manager1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ปิยะนุช ผู้จัดการ', 'manager@vibedaybkk.com', 'admin', 'active'),
('staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'นภัสวรรณ พนักงาน', 'staff1@vibedaybkk.com', 'editor', 'active'),
('staff2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'อรุณ ช่วยงาน', 'staff2@vibedaybkk.com', 'editor', 'active'),
('staff3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ชญานิศ ประสานงาน', 'staff3@vibedaybkk.com', 'editor', 'inactive'),
('designer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ภูมิพัฒน์ ออกแบบ', 'designer@vibedaybkk.com', 'editor', 'active'),
('marketing1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'กมลวรรณ การตลาด', 'marketing@vibedaybkk.com', 'editor', 'active'),
('support1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'วีรภัทร ซัพพอร์ต', 'support@vibedaybkk.com', 'editor', 'active');

-- Success message
SELECT 'Sample data inserted successfully!' as message,
       '10 Models' as models,
       '10 Articles' as articles,
       '10 Menus' as menus,
       '10 Contacts' as contacts,
       '10 Bookings' as bookings,
       '10 Users (password: password)' as users;



