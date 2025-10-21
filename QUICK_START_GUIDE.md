# 🚀 VIBEDAYBKK - Quick Start Guide

**วันที่:** 17 ตุลาคม 2025

---

## ⚡ เริ่มต้นใช้งานด่วน

### 1. สร้างฐานข้อมูลใหม่ (แนะนำ) 

เปิดใน Browser:
```
http://localhost:8888/vibedaybkk/create-new-database.php
```

สคริปต์จะทำให้อัตโนมัติ:
- ✅ สำรองฐานข้อมูลเดิม
- ✅ สร้างฐานข้อมูลใหม่
- ✅ Import ข้อมูลทั้งหมด
- ✅ ตรวจสอบความสมบูรณ์

### 2. ทดสอบการเชื่อมต่อ

```
http://localhost:8888/vibedaybkk/test-connection-all.php
```

ตรวจสอบ:
- ✅ การเชื่อมต่อฐานข้อมูล
- ✅ จำนวนตาราง (23 ตาราง)
- ✅ ข้อมูลในตารางสำคัญ

### 3. เข้าสู่ระบบ Admin

```
http://localhost:8888/vibedaybkk/admin/
```

**Login:**
- Username: `admin`
- Password: `admin123`

### 4. ตรวจสอบ Frontend

```
http://localhost:8888/vibedaybkk/
```

---

## 📚 เอกสารทั้งหมด

1. **COMPLETE_SYSTEM_DOCUMENTATION.md** ⭐ - เอกสารระบบฉบับสมบูรณ์
2. **PROJECT_STATUS.md** - สถานะโปรเจ็กต์
3. **README-FIX-ERROR-500.md** - แก้ไข Error 500
4. **QUICK_START_GUIDE.md** - เอกสารนี้

---

## 🗄️ ฐานข้อมูล

### ข้อมูลการเชื่อมต่อ

```php
Host: localhost
Port: 8889 (socket)
Socket: /Applications/MAMP/tmp/mysql/mysql.sock
Database: vibedaybkk
Username: root
Password: root
Charset: utf8mb4
```

### ตารางหลัก

| ตาราง | จำนวนข้อมูล | คำอธิบาย |
|-------|------------|----------|
| users | 13 | ผู้ใช้งานระบบ |
| roles | 4 | บทบาท (Programmer, Admin, Editor, Viewer) |
| permissions | 44 | สิทธิ์การเข้าถึง |
| settings | 84 | การตั้งค่าระบบ |
| homepage_sections | 8 | เนื้อหาหน้าแรก |
| categories | 6 | หมวดหมู่โมเดล |
| models | 10 | ข้อมูลโมเดล |
| articles | 10 | บทความ |
| bookings | 10 | การจอง |
| contacts | 22 | ข้อความติดต่อ |
| menus | 8 | เมนูนำทาง |
| activity_logs | 220 | บันทึกกิจกรรม |

---

## 🔐 ระบบสิทธิ์

### 4 บทบาท

1. **Programmer** (Level 100)
   - สิทธิ์สูงสุด
   - จัดการ Roles & Permissions
   - เข้าถึงทุกอย่าง

2. **Admin** (Level 80)
   - จัดการระบบ
   - สิทธิ์ตาม Permissions

3. **Editor** (Level 50)
   - จัดการเนื้อหา
   - ไม่สามารถจัดการผู้ใช้

4. **Viewer** (Level 10)
   - ดูอย่างเดียว
   - UI แสดง Readonly

### สิทธิ์แต่ละฟีเจอร์

- ✅ View - ดูข้อมูล
- ✅ Create - สร้างใหม่
- ✅ Edit - แก้ไข
- ✅ Delete - ลบ
- ✅ Export - ส่งออก

---

## 🎯 ฟีเจอร์หลัก

### Frontend
- ✅ หน้าแรกแบบ Single Page
- ✅ Responsive Design
- ✅ Preloader
- ✅ Reviews Carousel
- ✅ Contact Form
- ✅ Social Media Integration

### Admin
- ✅ Dashboard
- ✅ จัดการผู้ใช้ & Roles
- ✅ จัดการโมเดล & หมวดหมู่
- ✅ จัดการบทความ
- ✅ จัดการการจอง
- ✅ จัดการแกลเลอรี่
- ✅ จัดการหน้าแรก
- ✅ ตั้งค่าระบบ

---

## ⚠️ สิ่งสำคัญที่ต้องรู้

### การเชื่อมต่อฐานข้อมูล

**MAMP ต้องใช้ Socket Connection:**

```php
$conn = new mysqli(
    DB_HOST, 
    DB_USER, 
    DB_PASS, 
    DB_NAME, 
    0, 
    '/Applications/MAMP/tmp/mysql/mysql.sock'
);
```

### Upload Permissions

```bash
chmod -R 777 /Applications/MAMP/htdocs/vibedaybkk/uploads
```

### Config URL

```php
define('SITE_URL', 'http://localhost:8888/vibedaybkk');
```

---

## 🔧 การแก้ปัญหาเบื้องต้น

### ปัญหา: ไม่สามารถเชื่อมต่อฐานข้อมูล

**แก้ไข:**
1. ตรวจสอบ MAMP กำลังทำงาน
2. เปลี่ยนเป็น socket connection
3. ตรวจสอบ username/password

### ปัญหา: Error 500

**แก้ไข:**
1. เปิด error display
2. ตรวจสอบ error log
3. ตรวจสอบ config.php

### ปัญหา: Upload รูปไม่ได้

**แก้ไข:**
1. ตรวจสอบ permissions
2. ตรวจสอบ upload_max_filesize
3. ตรวจสอบโฟลเดอร์ uploads มีอยู่

---

## 📞 ติดต่อ & Support

- **Email:** admin@vibedaybkk.com
- **Phone:** 02-123-4567
- **Line:** @vibedaybkk

---

## ✅ Checklist การตรวจสอบ

- [ ] สร้างฐานข้อมูลเรียบร้อย
- [ ] ทดสอบการเชื่อมต่อผ่าน
- [ ] Login Admin ได้
- [ ] Dashboard แสดงข้อมูลถูกต้อง
- [ ] สามารถแก้ไขข้อมูลได้
- [ ] Upload รูปภาพได้
- [ ] Frontend แสดงผลถูกต้อง
- [ ] Mobile responsive ทำงานได้

---

**สถานะ:** 🟢 พร้อมใช้งาน

**Version:** 2.0

