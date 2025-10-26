# 🧪 คู่มือทดสอบด้วยตัวเอง - VIBEDAYBKK

**วันที่:** 17 ตุลาคม 2025  
**สถานะ:** ✅ แก้ไขบัคเสร็จแล้ว - กรุณาทดสอบ

---

## 🚀 เริ่มต้นทดสอบ

### 1. Login เข้าระบบ

```
http://localhost:8888/vibedaybkk/admin/login.php
```

**ข้อมูล Login:**
- Username: `admin`
- Password: `admin123`

---

## 📋 ทดสอบทีละฟีเจอร์

### ✅ Dashboard
```
http://localhost:8888/vibedaybkk/admin/
```
**ตรวจสอบ:**
- [ ] แสดงสถิติทั้งหมด
- [ ] แสดงโมเดลยอดนิยม
- [ ] แสดงข้อความล่าสุด
- [ ] แสดงการจองล่าสุด

---

### 👤 Models Management

**1. รายการโมเดล:**
```
http://localhost:8888/vibedaybkk/admin/models/
```
- [ ] แสดงรายการ (ว่างถ้ายังไม่มีข้อมูล)
- [ ] ค้นหาได้
- [ ] กรองตามหมวดหมู่/สถานะได้

**2. เพิ่มโมเดล:**
```
http://localhost:8888/vibedaybkk/admin/models/add.php
```
**ทดสอบเพิ่มข้อมูล:**
- กรอก: Code (TEST001), Name (ทดสอบโมเดล)
- เลือก Category
- กรอก ราคา, ส่วนสูง
- กด "บันทึก"
- [ ] บันทึกสำเร็จ
- [ ] redirect กลับไปหน้า list
- [ ] เห็นข้อมูลที่เพิ่ม

**3. แก้ไขโมเดล:**
- คลิกปุ่ม "แก้ไข" ที่โมเดลที่เพิ่ม
- แก้ไขชื่อ
- กด "บันทึก"
- [ ] แก้ไขสำเร็จ

**4. ลบโมเดล:**
- คลิกปุ่ม "ลบ"
- ยืนยันการลบ
- [ ] ลบสำเร็จ

---

### 📁 Categories Management

**1. รายการหมวดหมู่:**
```
http://localhost:8888/vibedaybkk/admin/categories/
```
- [ ] แสดงรายการ 6 หมวดหมู่
- [ ] แสดงจำนวนโมเดล

**2. เพิ่มหมวดหมู่:**
```
http://localhost:8888/vibedaybkk/admin/categories/add.php
```
**ทดสอบ:**
- Code: test-cat
- Name: ทดสอบหมวดหมู่
- [ ] บันทึกได้
- [ ] แก้ไขได้
- [ ] ลบได้ (ถ้าไม่มีโมเดล)

---

### 📰 Articles Management

**1. รายการบทความ:**
```
http://localhost:8888/vibedaybkk/admin/articles/
```
- [ ] แสดงรายการ
- [ ] ค้นหาได้
- [ ] กรองตามหมวดหมู่/สถานะ

**2. เพิ่มบทความ:**
```
http://localhost:8888/vibedaybkk/admin/articles/add.php
```
**ทดสอบ:**
- Title: บทความทดสอบ
- Content: เนื้อหาทดสอบ
- Category: เลือกหมวดหมู่
- [ ] บันทึกได้
- [ ] แก้ไขได้
- [ ] ลบได้

---

### 👥 Users Management

```
http://localhost:8888/vibedaybkk/admin/users/
```

**ทดสอบ:**
- [ ] แสดงรายการผู้ใช้
- [ ] เพิ่มผู้ใช้ใหม่ (username: testuser, password: test123)
- [ ] แก้ไข role
- [ ] ลบผู้ใช้ทดสอบ

---

### 📋 Menus Management

```
http://localhost:8888/vibedaybkk/admin/menus/
```

**ทดสอบ:**
- [ ] แสดงรายการเมนู
- [ ] เพิ่มเมนู (Title: ทดสอบ, URL: /test)
- [ ] แก้ไข
- [ ] ลบ

---

### 📅 Bookings & Contacts

**Bookings:**
```
http://localhost:8888/vibedaybkk/admin/bookings/
```
- [ ] แสดงรายการ (ว่างถ้ายังไม่มี)
- [ ] คลิก "ดู" ได้ (ถ้ามีข้อมูล)

**Contacts:**
```
http://localhost:8888/vibedaybkk/admin/contacts/
```
- [ ] แสดงรายการ
- [ ] คลิก "ดู" ได้
- [ ] เปลี่ยนสถานะได้

---

### 🖼️ Gallery Management

**1. Gallery:**
```
http://localhost:8888/vibedaybkk/admin/gallery/
```
- [ ] แสดงรายการรูปภาพ
- [ ] upload รูปใหม่ได้

**2. Albums:**
```
http://localhost:8888/vibedaybkk/admin/gallery/albums.php
```
- [ ] แสดงรายการอัลบั้ม
- [ ] สร้างอัลบั้มใหม่ได้

---

### 🏠 Homepage Management ⭐ แก้ไขแล้ว

**1. Homepage List:**
```
http://localhost:8888/vibedaybkk/admin/homepage/
```
- [ ] แสดงรายการ 8 sections
- [ ] ไม่มี Warning
- [ ] Toggle เปิด/ปิด ทำงาน

**2. Edit Section:**
```
http://localhost:8888/vibedaybkk/admin/homepage/edit.php?id=1
```
**ทดสอบ:**
- [ ] ไม่มี Warning
- [ ] ไม่มี Deprecated
- [ ] แสดงฟอร์มครบถ้วน
- [ ] แก้ไข Title ได้
- [ ] แก้ไข Subtitle ได้
- [ ] แก้ไข Content ได้
- [ ] แก้ไข Button 1 & 2 ได้
- [ ] Upload รูปพื้นหลังได้
- [ ] บันทึกสำเร็จ

---

### ⚙️ Settings

**1. General Settings:**
```
http://localhost:8888/vibedaybkk/admin/settings/
```
- [ ] แสดงการตั้งค่าทั้งหมด
- [ ] แก้ไขได้
- [ ] Upload logo ได้

**2. SEO Settings:**
```
http://localhost:8888/vibedaybkk/admin/settings/seo.php
```
- [ ] แก้ไข Meta Title/Description ได้
- [ ] บันทึกสำเร็จ

**3. Social Settings:**
```
http://localhost:8888/vibedaybkk/admin/settings/social.php
```
- [ ] แก้ไข Social Links ได้
- [ ] Toggle เปิด/ปิด ทำงาน

---

### 🔐 Roles & Permissions

```
http://localhost:8888/vibedaybkk/admin/roles/
```

**ทดสอบ:**
- [ ] แสดงรายการ Roles 4 roles
- [ ] คลิก "แก้ไขสิทธิ์" ได้
- [ ] Toggle permissions ทำงาน
- [ ] Quick Set buttons ทำงาน

---

### ⭐ Reviews

```
http://localhost:8888/vibedaybkk/admin/reviews/
```

**ทดสอบ:**
- [ ] แสดงรายการรีวิว
- [ ] เพิ่มรีวิวใหม่ได้
- [ ] แก้ไขได้
- [ ] ลบได้

---

## 🌐 Frontend

```
http://localhost:8888/vibedaybkk/
```

**ตรวจสอบ:**
- [ ] หน้าแรกแสดงผลสมบูรณ์
- [ ] ไม่มี error ในหน้า
- [ ] Hero section แสดงถูกต้อง
- [ ] About section แสดงถูกต้อง
- [ ] Services แสดงหมวดหมู่
- [ ] Reviews carousel ทำงาน
- [ ] Contact form ส่งได้

---

## ❌ ถ้าเจอ Error

**1. เปิด Developer Tools:**
- กด `F12` หรือ `Cmd + Option + I`

**2. ดู Console:**
- แสดง JavaScript errors

**3. ดู Network:**
- แสดง HTTP errors
- คลิกที่ request ที่ error
- ดู Response

**4. แจ้งข้อมูล:**
- Screenshot ทั้งหน้า
- Copy error message ทั้งหมด
- บอกว่ากดปุ่มไหน/ทำอะไร
- URL ที่ error

---

## ✅ Checklist สุดท้าย

### ทดสอบครบทุกโมดูล:
- [ ] Dashboard
- [ ] Models (List, Add, Edit, Delete)
- [ ] Categories (List, Add, Edit, Delete)
- [ ] Articles (List, Add, Edit, Delete)
- [ ] Users (List, Add, Edit, Delete)
- [ ] Menus (List, Add, Edit, Delete)
- [ ] Bookings (List, View)
- [ ] Contacts (List, View, Reply)
- [ ] Gallery (List, Upload, Albums)
- [ ] Homepage (List, Edit) ⭐
- [ ] Settings (General, SEO, Social)
- [ ] Roles (List, Edit Permissions)
- [ ] Reviews (List, Add, Edit, Delete)

### ไม่มี Error:
- [ ] ไม่มี Fatal Error
- [ ] ไม่มี Warning
- [ ] ไม่มี Deprecated
- [ ] ไม่มีหน้าขาว

---

## 📞 แจ้งปัญหา

ถ้าเจอบัคเพิ่มเติม กรุณาแจ้ง:

1. **หน้าไหน** (URL เต็ม)
2. **ทำอะไร** (กดปุ่มไหน, กรอกอะไร)
3. **Error อะไร** (copy ทั้งหมด)
4. **Screenshot** (ถ้าสะดวก)

---

**สถานะ:** 🟢 พร้อมทดสอบ  
**บัคที่แก้:** 55+ รายการ  
**ไฟล์ที่แก้:** 20+ ไฟล์




