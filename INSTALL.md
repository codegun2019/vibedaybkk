# คู่มือการติดตั้งระบบ VIBEDAYBKK Management System

## 📋 สารบัญ
1. [ความต้องการของระบบ](#ความต้องการของระบบ)
2. [การติดตั้ง](#การติดตั้ง)
3. [โครงสร้างฐานข้อมูล](#โครงสร้างฐานข้อมูล)
4. [การใช้งาน](#การใช้งาน)
5. [ฟีเจอร์ของระบบ](#ฟีเจอร์ของระบบ)

---

## 🔧 ความต้องการของระบบ

- **PHP**: เวอร์ชัน 7.4 หรือสูงกว่า
- **MySQL**: เวอร์ชัน 5.7 หรือสูงกว่า (แนะนำ MySQL 8.0+)
- **Web Server**: Apache หรือ Nginx
- **PHP Extensions**:
  - mysqli
  - pdo
  - pdo_mysql
  - mbstring
  - fileinfo
  - gd (สำหรับจัดการรูปภาพ)

---

## 📦 การติดตั้ง

### ขั้นตอนที่ 1: Import ฐานข้อมูล

1. เปิด phpMyAdmin หรือ MySQL Client
2. สร้างฐานข้อมูลใหม่ชื่อ `vibedaybkk`
3. Import ไฟล์ `database.sql`:
   ```sql
   mysql -u root -p vibedaybkk < database.sql
   ```
   หรือใช้ phpMyAdmin: Import > เลือกไฟล์ `database.sql` > Go

### ขั้นตอนที่ 2: ตั้งค่า Config

แก้ไขไฟล์ `includes/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');      // เปลี่ยนตามเซิร์ฟเวอร์
define('DB_USER', 'root');           // Username
define('DB_PASS', '');               // Password
define('DB_NAME', 'vibedaybkk');     // ชื่อฐานข้อมูล

// Site Configuration
define('SITE_URL', 'http://localhost/vibedaybkk');  // URL เว็บไซต์
```

### ขั้นตอนที่ 3: สร้างโฟลเดอร์ Uploads

```bash
mkdir uploads
chmod 755 uploads
mkdir uploads/models
mkdir uploads/articles
mkdir uploads/general
```

### ขั้นตอนที่ 4: เข้าสู่ระบบ Admin

1. เปิดเบราว์เซอร์ไปที่: `http://localhost/vibedaybkk/admin/login.php`
2. ใช้ข้อมูลเริ่มต้น:
   - **Username**: `admin`
   - **Password**: `admin123`
3. **สำคัญ**: เปลี่ยนรหัสผ่านทันทีหลังเข้าสู่ระบบครั้งแรก!

---

## 🗄️ โครงสร้างฐานข้อมูล

### ตารางหลัก (11 ตาราง)

#### 1. `users` - ผู้ใช้งานระบบ
- สำหรับ Login เข้าระบบ Admin
- บทบาท: admin, editor
- เก็บข้อมูล: username, password (hashed), email, role

#### 2. `categories` - หมวดหมู่บริการ
- จัดการประเภทโมเดล (แฟชั่น, ถ่ายภาพ, อีเวนต์, ฟิตเนส, ธุรกิจ)
- CRUD: เพิ่ม/ลบ/แก้ไขได้ทั้งหมด
- เก็บข้อมูล: ชื่อ, ราคา, ไอคอน, สี, เพศ

#### 3. `models` - ข้อมูลโมเดล ⭐ สำคัญที่สุด
- **ข้อมูลโมเดล**:
  - ชื่อ, รหัส, คำอธิบาย
  - ราคา (min-max)
  - ส่วนสูง, น้ำหนัก, รอบอก-เอว-สะโพก
  - อายุ, ประสบการณ์
  - สถานะ: available, busy, inactive
- **สถิติ**: จำนวนการดู, การจอง, คะแนน

#### 4. `model_images` - รูปภาพโมเดล
- เก็บรูปภาพหลายรูปต่อ 1 โมเดล
- ประเภท: profile, portfolio, cover
- กำหนดรูปหลัก (is_primary)

#### 5. `model_requirements` - คุณสมบัติที่ต้องการ
- คุณสมบัติของแต่ละหมวดหมู่
- เช่น: "ส่วนสูง 165-175 cm", "มีพอร์ตโฟลิโอ"

#### 6. `articles` - บทความ
- จัดการบทความบนเว็บไซต์
- สถานะ: draft, published, archived
- SEO: slug, excerpt, read_time

#### 7. `menus` - เมนูนำทาง
- จัดการเมนูด้านบน
- รองรับ parent-child (เมนูซ้อน)

#### 8. `contacts` - ข้อมูลติดต่อ
- เก็บข้อมูลจากฟอร์ม Contact
- สถานะ: new, read, replied, closed

#### 9. `bookings` - การจองโมเดล
- บันทึกการจองของลูกค้า
- สถานะ: pending, confirmed, cancelled, completed
- เก็บข้อมูล: วันที่, จำนวนวัน, ราคา

#### 10. `settings` - ตั้งค่าระบบ
- ตั้งค่าทั่วไป: ชื่อเว็บ, อีเมล, เบอร์โทร
- ตั้งค่าระบบ: items_per_page, booking_advance_days

#### 11. `activity_logs` - บันทึกการใช้งาน
- เก็บ log การทำงานของ admin
- ติดตาม: create, update, delete

---

## 🎯 ฟีเจอร์ของระบบ

### 1. ระบบ Login และ Authentication
- ✅ เข้าสู่ระบบด้วย username/password
- ✅ Session management
- ✅ CSRF protection
- ✅ Password hashing (bcrypt)
- ✅ Remember me (30 days)
- ✅ Log การเข้าสู่ระบบ

### 2. ระบบจัดการโมเดล (Models Management) ⭐
- ✅ เพิ่ม/แก้ไข/ลบโมเดล
- ✅ Upload รูปภาพหลายรูป
- ✅ กำหนดรูปภาพหลัก
- ✅ จัดการข้อมูลครบถ้วน:
  - ข้อมูลส่วนตัว
  - รูปร่าง/ขนาด
  - ราคา
  - ประสบการณ์
  - สกิล/ความสามารถ
- ✅ Badge "Featured" สำหรับโมเดลแนะนำ
- ✅ สถิติ: view, booking, rating
- ✅ เรียงลำดับและกรอง

### 3. ระบบจัดการหมวดหมู่ (Categories Management)
- ✅ เพิ่ม/แก้ไข/ลบหมวดหมู่
- ✅ กำหนดไอคอน Font Awesome
- ✅ กำหนดสี Gradient
- ✅ กำหนดเพศ (female/male/all)
- ✅ กำหนดช่วงราคา
- ✅ จัดเรียงลำดับ (drag & drop)
- ✅ เชื่อมกับหน้าบ้านอัตโนมัติ

### 4. ระบบจัดการบทความ (Articles Management)
- ✅ สร้าง/แก้ไข/ลบบทความ
- ✅ WYSIWYG Editor
- ✅ Upload featured image
- ✅ Auto-generate slug
- ✅ จัดการหมวดหมู่
- ✅ สถานะ: แบบร่าง/เผยแพร่
- ✅ กำหนดเวลาอ่าน
- ✅ นับจำนวนผู้เข้าชม

### 5. ระบบจัดการเมนู (Menu Management)
- ✅ เพิ่ม/แก้ไข/ลบเมนู
- ✅ เมนูแบบซ้อน (parent-child)
- ✅ จัดเรียงลำดับ
- ✅ เปิด-ปิดการใช้งาน
- ✅ กำหนด target (_self/_blank)

### 6. ระบบจัดการรูปภาพ (Image Management)
- ✅ Upload หลายรูปพร้อมกัน
- ✅ ตรวจสอบประเภทและขนาดไฟล์
- ✅ สร้างชื่อไฟล์ unique
- ✅ จัดเก็บตามโฟลเดอร์
- ✅ ลบรูปเก่าเมื่อเปลี่ยน
- ✅ รองรับ: JPG, PNG, GIF, WEBP

### 7. ฟอร์มติดต่อ (Contact Form)
- ✅ รับข้อมูลจากหน้าบ้าน
- ✅ แจ้งเตือนอีเมล
- ✅ จัดการสถานะ
- ✅ ตอบกลับลูกค้า
- ✅ เก็บ IP และ User Agent

### 8. ระบบการจอง (Booking System)
- ✅ รับการจองจากหน้าบ้าน
- ✅ ยืนยัน/ยกเลิกการจอง
- ✅ คำนวณราคาอัตโนมัติ
- ✅ ตรวจสอบความพร้อมของโมเดล
- ✅ รายงานการจอง

### 9. Dashboard และรายงาน
- ✅ สรุปภาพรวมระบบ
- ✅ สถิติโมเดลยอดนิยม
- ✅ การจองล่าสุด
- ✅ ข้อความติดต่อใหม่
- ✅ รายได้รวม
- ✅ กราฟแสดงข้อมูล

### 10. ตั้งค่าระบบ (Settings)
- ✅ ข้อมูลเว็บไซต์
- ✅ ข้อมูลติดต่อ
- ✅ การตั้งค่าต่างๆ
- ✅ จำนวนรายการต่อหน้า

---

## 🔐 ความปลอดภัย (Security Features)

- ✅ **SQL Injection Protection**: Prepared Statements
- ✅ **XSS Protection**: Input sanitization
- ✅ **CSRF Protection**: Token-based
- ✅ **Password Security**: Bcrypt hashing
- ✅ **Session Security**: HttpOnly cookies
- ✅ **File Upload Security**: Type & size validation
- ✅ **Access Control**: Role-based permissions
- ✅ **Activity Logging**: Track all actions

---

## 📁 โครงสร้างโฟลเดอร์

```
vibedaybkk/
├── admin/                      # Admin Panel
│   ├── index.php              # Dashboard
│   ├── login.php              # Login page
│   ├── logout.php             # Logout
│   ├── models/                # จัดการโมเดล
│   ├── categories/            # จัดการหมวดหมู่
│   ├── articles/              # จัดการบทความ
│   ├── menus/                 # จัดการเมนู
│   ├── contacts/              # จัดการติดต่อ
│   ├── bookings/              # จัดการจอง
│   ├── settings/              # ตั้งค่า
│   └── users/                 # จัดการผู้ใช้
├── includes/                  # ไฟล์ที่ใช้ร่วมกัน
│   ├── config.php            # Configuration
│   └── functions.php         # Functions library
├── uploads/                   # ไฟล์ที่อัพโหลด
│   ├── models/               # รูปโมเดล
│   ├── articles/             # รูปบทความ
│   └── general/              # รูปทั่วไป
├── api/                      # API Endpoints (ถ้ามี)
├── database.sql              # SQL Schema
├── index.html                # หน้าแรก (Frontend)
├── services.html             # หน้าบริการ
├── articles.html             # หน้าบทความ
└── INSTALL.md               # คู่มือนี้
```

---

## 🚀 ขั้นตอนถัดไป

### สำหรับ Developer:

1. **ติดตั้งระบบตามคู่มือข้างต้น**
2. **สร้างไฟล์ Admin Panel**:
   - `admin/login.php` - หน้าเข้าสู่ระบบ
   - `admin/index.php` (dashboard.php) - หน้า Dashboard
   - `admin/models/` - CRUD โมเดล
   - `admin/categories/` - CRUD หมวดหมู่
3. **เชื่อมต่อกับหน้าบ้าน**:
   - แก้ไข `services-detail.html` ให้ดึงข้อมูลจากฐานข้อมูล
   - แปลงเป็น `services-detail.php`
4. **ทดสอบระบบ**

---

## ❗ ข้อควรระวัง

1. **อย่าลืมเปลี่ยนรหัสผ่าน admin เริ่มต้น**
2. **ตั้งค่า permission โฟลเดอร์ uploads ให้ถูกต้อง** (755)
3. **Backup ฐานข้อมูลเป็นประจำ**
4. **ใช้ HTTPS ใน production**
5. **ตรวจสอบ PHP error_reporting ใน production** (ปิด display_errors)

---

## 📞 Support

หากพบปัญหาในการติดตั้งหรือใช้งาน:
- อ่าน documentation อีกครั้ง
- ตรวจสอบ PHP error log
- ตรวจสอบ MySQL error log
- ตรวจสอบ browser console

---

## 📝 Changelog

### Version 1.0.0 (2025-10-14)
- ✅ สร้างฐานข้อมูล 11 ตาราง
- ✅ สร้าง Config และ Functions
- ✅ ออกแบบโครงสร้างระบบ
- ✅ เตรียมความพร้อมสำหรับ Admin Panel

---

**หมายเหตุ**: ระบบนี้เป็น Basic PHP (ไม่ใช้ Framework) ออกแบบมาเพื่อความเรียบง่ายและง่ายต่อการปรับแต่ง

