# VIBEDAYBKK Admin Panel

ระบบจัดการ Backend สำหรับเว็บไซต์ VIBEDAYBKK

## 🔐 การเข้าสู่ระบบ

**URL**: `http://localhost/vibedaybkk/admin/login.php`

**ข้อมูลเริ่มต้น**:
- Username: `admin`
- Password: `admin123`

⚠️ **สำคัญ**: เปลี่ยนรหัสผ่านทันทีหลังเข้าสู่ระบบครั้งแรก!

---

## 📁 โครงสร้างระบบ

### ระบบที่พร้อมใช้งาน ✅:

1. **Login System** (`login.php`, `logout.php`)
   - เข้าสู่ระบบด้วย username/password
   - Remember me (30 วัน)
   - CSRF protection

2. **Dashboard** (`index.php`)
   - สรุปสถิติทั้งหมด
   - โมเดลยอดนิยม Top 5
   - ข้อความติดต่อล่าสุด
   - การจองล่าสุด
   - Quick actions

3. **Models Management** (`models/`)
   - ✅ `index.php` - รายการโมเดลทั้งหมด
   - ✅ `add.php` - เพิ่มโมเดลใหม่
   - ✅ `edit.php` - แก้ไขข้อมูลโมเดล
   - ✅ `delete.php` - ลบโมเดล
   - ✅ `delete-image.php` - ลบรูปภาพ
   - ✅ `set-primary.php` - ตั้งรูปหลัก

4. **Categories Management** (`categories/`)
   - ✅ `index.php` - รายการหมวดหมู่
   - ✅ `add.php` - เพิ่มหมวดหมู่
   - ✅ `edit.php` - แก้ไขหมวดหมู่
   - ✅ `delete.php` - ลบหมวดหมู่

5. **Contacts Management** (`contacts/`)
   - ✅ `index.php` - รายการข้อความติดต่อ
   - ✅ `view.php` - ดูและตอบกลับข้อความ
   - ✅ `delete.php` - ลบข้อความ

6. **Settings** (`settings/`)
   - ✅ `index.php` - ตั้งค่าระบบ
   - ข้อมูลเว็บไซต์
   - โซเชียลมีเดีย
   - การตั้งค่าทั่วไป

---

## 🎯 ฟีเจอร์หลัก

### จัดการโมเดล ⭐ (สำคัญที่สุด)
- ✅ เพิ่ม/แก้ไข/ลบโมเดล
- ✅ Upload รูปภาพหลายรูป
- ✅ กำหนดรูปหลัก
- ✅ ข้อมูลครบถ้วน:
  - ข้อมูลพื้นฐาน (ชื่อ, รหัส, คำอธิบาย)
  - ข้อมูลรูปร่าง (ส่วนสูง, น้ำหนัก, รอบอก-เอว-สะโพก)
  - ราคา (ขั้นต่ำ-สูงสุด)
  - ประสบการณ์, อายุ, ภาษา, ทักษะ
  - สถานะ (ว่าง/ไม่ว่าง/ไม่ใช้งาน)
  - Featured (โมเดลแนะนำ)
- ✅ ค้นหาและกรองข้อมูล
- ✅ Pagination
- ✅ สถิติ (views, bookings, rating)

### จัดการหมวดหมู่
- ✅ CRUD หมวดหมู่บริการ
- ✅ กำหนดไอคอน Font Awesome
- ✅ กำหนดสี Gradient
- ✅ กำหนดเพศ (female/male/all)
- ✅ กำหนดช่วงราคา
- ✅ จัดเรียงลำดับ
- ✅ คุณสมบัติที่ต้องการ (Requirements)
- ✅ ป้องกันลบหมวดหมู่ที่มีโมเดล

### จัดการข้อความติดต่อ
- ✅ ดูข้อความทั้งหมด
- ✅ กรองตามสถานะ (ใหม่, อ่านแล้ว, ตอบกลับแล้ว, ปิด)
- ✅ ตอบกลับข้อความ
- ✅ เปลี่ยนสถานะ
- ✅ ลบข้อความ

### Dashboard
- ✅ สรุปสถิติ 4 ช่อง
- ✅ สถิติย่อย 3 ช่อง
- ✅ โมเดลยอดนิยม Top 5
- ✅ ข้อความติดต่อล่าสุด 5 ข้อความ
- ✅ การจองล่าสุด 5 รายการ
- ✅ Quick Actions (เพิ่มโมเดล, หมวดหมู่, บทความ)

---

## 🔒 ระบบความปลอดภัย

- ✅ **SQL Injection Protection** - Prepared Statements (PDO)
- ✅ **XSS Protection** - htmlspecialchars(), clean_input()
- ✅ **CSRF Protection** - Token-based validation
- ✅ **Password Security** - Bcrypt hashing
- ✅ **Session Security** - HttpOnly cookies
- ✅ **File Upload Security** - Type & size validation
- ✅ **Access Control** - require_login(), require_admin()
- ✅ **Activity Logging** - บันทึกทุกการกระทำ

---

## 📊 ฐานข้อมูล

### ตารางสำคัญ:

1. **models** - ข้อมูลโมเดล (36 คอลัมน์)
2. **categories** - หมวดหมู่บริการ
3. **model_images** - รูปภาพโมเดล (หลายรูปต่อ 1 โมเดล)
4. **model_requirements** - คุณสมบัติของแต่ละหมวดหมู่
5. **contacts** - ข้อความติดต่อจากลูกค้า
6. **bookings** - การจองโมเดล
7. **articles** - บทความ
8. **menus** - เมนูนำทาง
9. **settings** - ตั้งค่าระบบ
10. **users** - ผู้ใช้งาน admin
11. **activity_logs** - บันทึกการใช้งาน

---

## 🎨 การออกแบบ

- **Framework**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.0.0
- **Font**: Kanit (Google Fonts)
- **Colors**:
  - Primary: #DC2626 (Red)
  - Success: #10B981 (Green)
  - Warning: #F59E0B (Orange)
  - Info: #3B82F6 (Blue)

---

## 📝 การใช้งาน

### 1. จัดการโมเดล

**เพิ่มโมเดลใหม่**:
1. คลิก "เพิ่มโมเดลใหม่"
2. กรอกข้อมูล:
   - หมวดหมู่ (required)
   - รหัสโมเดล (required, unique)
   - ชื่อ (required)
   - ข้อมูลรูปร่าง (ส่วนสูง, น้ำหนัก, รอบอก-เอว-สะโพก)
   - ราคา (ขั้นต่ำ-สูงสุด)
   - ประสบการณ์, ภาษา, ทักษะ
3. Upload รูปภาพ (รูปแรกจะเป็นรูปหลัก)
4. เลือกสถานะ
5. คลิก "บันทึก"

**แก้ไขโมเดล**:
1. คลิกปุ่ม "แก้ไข" ที่รายการโมเดล
2. แก้ไขข้อมูล
3. เพิ่มรูปภาพเพิ่มเติม
4. ตั้งรูปหลัก / ลบรูป
5. คลิก "บันทึกการแก้ไข"

**ลบโมเดล**:
- คลิกปุ่ม "ลบ" (จะลบรูปภาพทั้งหมดด้วย)

### 2. จัดการหมวดหมู่

**เพิ่มหมวดหมู่**:
1. กรอกรหัส (เช่น female-fashion)
2. กรอกชื่อไทยและอังกฤษ
3. เลือกเพศ (female/male/all)
4. กรอกไอคอน Font Awesome (เช่น fa-female)
5. กรอกสี Gradient (เช่น from-pink-500 to-red-primary)
6. กรอกช่วงราคา
7. กรอกคุณสมบัติ (แยกแต่ละบรรทัด)
8. กำหนดลำดับและสถานะ
9. คลิก "บันทึก"

**แก้ไขหมวดหมู่**:
- แก้ไขข้อมูลตามต้องการ
- อัพเดทคุณสมบัติ
- คลิก "บันทึกการแก้ไข"

**ลบหมวดหมู่**:
- สามารถลบได้เฉพาะหมวดหมู่ที่ไม่มีโมเดล

### 3. จัดการข้อความติดต่อ

**ดูข้อความ**:
- คลิกที่ข้อความเพื่อดูรายละเอียด
- ข้อความใหม่จะแสดงสีเหลือง

**ตอบกลับ**:
1. เปิดข้อความ
2. พิมพ์ข้อความตอบกลับ
3. คลิก "ส่งข้อความตอบกลับ"

**จัดการสถานะ**:
- อ่านแล้ว
- ตอบกลับแล้ว
- ปิดเคส

---

## 🚀 ระบบที่ยังต้องสร้าง (TODO)

### ระบบที่ยังไม่มี:
- ❌ **Articles Management** (CRUD บทความ)
- ❌ **Menus Management** (CRUD เมนู)
- ❌ **Bookings Management** (จัดการการจอง)
- ❌ **Users Management** (จัดการผู้ใช้)

### Frontend Integration:
- ❌ แปลง `services-detail.html` เป็น PHP
- ❌ แปลง `articles.html` เป็น PHP
- ❌ ฟอร์มติดต่อที่ส่งข้อมูลเข้า DB จริง

---

## 📝 Tips

### การ Upload รูปภาพ:
- ขนาดไฟล์สูงสุด: 5MB
- ไฟล์ที่รองรับ: JPG, PNG, GIF, WEBP
- แต่ละโมเดลสามารถมีหลายรูป
- กำหนดรูปหลักได้

### การจัดเรียง:
- ใช้ฟิลด์ `sort_order`
- เลขน้อยแสดงก่อน
- 0 = default

### Activity Logs:
- บันทึกทุกการกระทำ (create, update, delete)
- เก็บ IP address และ User Agent
- ดูได้ใน Dashboard

---

## ⚡ Performance

- ใช้ Indexes สำหรับตารางหลัก
- Prepared Statements ป้องกัน SQL Injection
- Session-based authentication
- Optimized queries

---

## 🐛 Troubleshooting

### ไม่สามารถ Login ได้:
1. ตรวจสอบข้อมูล username/password
2. ตรวจสอบการเชื่อมต่อฐานข้อมูล
3. ตรวจสอบ session configuration

### Upload รูปไม่ได้:
1. ตรวจสอบ permission โฟลเดอร์ `uploads/` (755)
2. ตรวจสอบขนาดไฟล์ (สูงสุด 5MB)
3. ตรวจสอบประเภทไฟล์

### แสดงข้อผิดพลาด PHP:
1. เปิด error_reporting ใน `config.php`
2. ตรวจสอบ PHP error log
3. ตรวจสอบ MySQL error log

---

## 📞 Support

หากพบปัญหา:
1. อ่าน `INSTALL.md` 
2. ตรวจสอบ error logs
3. ตรวจสอบ browser console

---

**Version**: 1.0.0  
**Last Updated**: October 14, 2025  
**Developer**: VIBEDAYBKK Team

