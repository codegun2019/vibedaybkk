# 📚 คู่มือฟีเจอร์ Admin Panel - VIBEDAYBKK

---

## 🏠 Dashboard

**URL:** `/admin/`

**ฟีเจอร์:**
- สถิติโมเดลทั้งหมด
- สถิติโมเดลว่าง
- การจองที่รอดำเนินการ
- รายได้รวม
- หมวดหมู่บริการ
- บทความที่เผยแพร่
- ข้อความใหม่
- โมเดลยอดนิยม Top 5
- ข้อความติดต่อล่าสุด
- การจองล่าสุด
- Quick Actions (ลิงก์ด่วน)

---

## 👤 Models Management

**URL:** `/admin/models/`

### ฟีเจอร์:

#### 1. รายการโมเดล (index.php)
- แสดงรายการโมเดลทั้งหมด (แบบ pagination)
- ค้นหาโดย: ชื่อ, รหัส, คำอธิบาย
- กรองตาม: หมวดหมู่, สถานะ
- แสดง: รูปภาพ, รหัส, ชื่อ, หมวดหมู่, ส่วนสูง, ราคา, ประสบการณ์, featured, สถานะ
- การกระทำ: แก้ไข, ลบ (ตามสิทธิ์)

#### 2. เพิ่มโมเดล (add.php)
**ข้อมูลที่กรอกได้:**
- หมวดหมู่ *
- รหัสโมเดล * (ไม่ซ้ำ)
- ชื่อ (ไทย) *
- ชื่อ (อังกฤษ)
- คำอธิบาย
- ราคาต่ำสุด-สูงสุด
- ส่วนสูง (cm)
- น้ำหนัก (kg)
- รอบอก-เอว-สะโพก (inches)
- ประสบการณ์ (ปี)
- อายุ
- สีผิว
- สีผม
- สีตา
- ภาษาที่พูดได้
- ทักษะพิเศษ
- แนะนำ (Featured)
- สถานะ: ว่าง/ไม่ว่าง/ไม่ใช้งาน
- ลำดับแสดงผล
- **รูปภาพหลายรูป** (multiple upload)

#### 3. แก้ไขโมเดล (edit.php)
- แก้ไขข้อมูลทั้งหมดได้
- อัพโหลดรูปเพิ่ม
- ลบรูปได้
- ตั้งรูปหลักได้

#### 4. ลบโมเดล (delete.php)
- ลบโมเดลและรูปภาพทั้งหมด
- Log activity

---

## 📁 Categories Management

**URL:** `/admin/categories/`

### ฟีเจอร์:

#### 1. รายการหมวดหมู่ (index.php)
- แสดงทุกหมวดหมู่
- จำนวนโมเดลในแต่ละหมวดหมู่
- ไอคอนและสี
- ช่วงราคา
- สถานะ

#### 2. เพิ่มหมวดหมู่ (add.php)
**ข้อมูลที่กรอก:**
- รหัส * (ไม่ซ้ำ)
- เพศ: ทั้งหมด/หญิง/ชาย
- ชื่อ (ไทย) *
- ชื่อ (อังกฤษ)
- คำอธิบาย
- ไอคอน (Font Awesome)
- สี (Tailwind gradient)
- ราคาต่ำสุด-สูงสุด
- ลำดับ
- สถานะ: ใช้งาน/ไม่ใช้งาน
- คุณสมบัติที่ต้องการ (แต่ละบรรทัด)

---

## 📰 Articles Management

**URL:** `/admin/articles/`

### ฟีเจอร์:

#### 1. บทความ (index.php, add.php, edit.php)
**ข้อมูล:**
- หัวข้อบทความ *
- Slug (auto-generate)
- หมวดหมู่บทความ
- เนื้อหาสั้น (excerpt)
- เนื้อหาเต็ม (content editor)
- รูปภาพหลัก
- เวลาอ่าน (นาที)
- สถานะ: draft/published
- วันที่เผยแพร่

#### 2. หมวดหมู่บทความ (/article-categories/)
- เพิ่ม/แก้ไข/ลบหมวดหมู่บทความ
- Slug, ไอคอน, สี

---

## 👥 Users Management

**URL:** `/admin/users/`

### ฟีเจอร์:

#### Roles (4 ระดับ):
1. **Programmer** (Level 100) - ทุกอย่าง
2. **Admin** (Level 80) - จัดการทั้งหมด
3. **Editor** (Level 50) - จัดการเนื้อหา
4. **Viewer** (Level 10) - ดูอย่างเดียว

#### ข้อมูลผู้ใช้:
- Username * (ไม่ซ้ำ)
- Password *
- Full Name *
- Email *
- Role *
- สถานะ: active/inactive

**การกระทำ:**
- เพิ่มผู้ใช้ (ไม่ให้สร้าง programmer)
- แก้ไขข้อมูล
- ลบผู้ใช้ (ไม่ให้ลบตัวเอง, ไม่ให้ลบ programmer)

---

## 📋 Menus Management

**URL:** `/admin/menus/`

### ฟีเจอร์:

#### ข้อมูลเมนู:
- หัวข้อเมนู *
- URL/ลิงก์ *
- ไอคอน (Font Awesome)
- เมนูหลัก (Parent Menu) - สร้างเมนูย่อยได้
- Target: _self/_blank
- ลำดับ (drag & drop)
- สถานะ

---

## 📅 Bookings Management

**URL:** `/admin/bookings/`

### ฟีเจอร์:

#### ดูรายการจอง (index.php):
- รหัสการจอง
- โมเดล
- ชื่อลูกค้า
- วันที่จอง
- จำนวนวัน
- ประเภทบริการ
- ราคารวม
- สถานะ: pending/confirmed/completed/cancelled
- สถานะการชำระเงิน

#### ดูรายละเอียด (view.php):
- ข้อมูลลูกค้า
- ข้อมูลการจอง
- ข้อความ
- เปลี่ยนสถานะได้
- เปลี่ยนสถานะการชำระเงิน

---

## 📧 Contacts Management

**URL:** `/admin/contacts/`

### ฟีเจอร์:

#### รายการข้อความ (index.php):
- ชื่อผู้ติดต่อ
- Email
- เบอร์โทร
- ประเภทบริการ
- ข้อความ
- สถานะ: new/read/replied
- วันที่

#### ดูและตอบกลับ (view.php):
- ข้อมูลผู้ติดต่อ
- ข้อความเต็ม
- IP Address
- User Agent
- ตอบกลับได้
- เปลี่ยนสถานะเป็น "ตอบแล้ว"

---

## 🖼️ Gallery Management

**URL:** `/admin/gallery/`

### ฟีเจอร์:

#### 1. Gallery Images (index.php)
- แสดงรูปทั้งหมดแบบ grid
- กรองตามอัลบั้ม
- ค้นหาตามชื่อ
- จำนวน views
- แก้ไข: title, description
- ลบรูป

#### 2. Albums (albums.php)
- สร้างอัลบั้ม
- แก้ไขอัลบั้ม
- ลบอัลบั้ม (ถ้าไม่มีรูป)
- ดูจำนวนรูปในแต่ละอัลบั้ม

#### 3. Upload (upload.php)
- อัพโหลดหลายรูปพร้อมกัน
- เลือกอัลบั้ม
- ตั้ง title, description
- Auto-generate thumbnail

---

## 🏠 Homepage Management ⭐

**URL:** `/admin/homepage/`

### Sections (8 sections):

1. **Hero** - หน้าหลัก
2. **About** - เกี่ยวกับเรา
3. **Services** - บริการ
4. **Gallery** - ผลงาน
5. **Reviews** - รีวิว
6. **Stats** - ตัวเลข
7. **CTA** - Call to Action
8. **Contact** - ติดต่อ

### ข้อมูลแต่ละ Section:

**เนื้อหา:**
- Title (หัวข้อ)
- Subtitle (หัวข้อรอง)
- Content (เนื้อหา)

**ปุ่ม:**
- Button 1: Text + Link
- Button 2: Text + Link

**พื้นหลัง:**
- Background Type: color/image
- Background Color
- Background Image (upload)
- Background Position: center/top/bottom/left/right
- Background Size: cover/contain/auto
- Background Repeat: no-repeat/repeat/repeat-x/repeat-y
- Background Attachment: scroll/fixed

**รูปภาพ:**
- Left Image (สำหรับ About section)

**อื่นๆ:**
- ลำดับการแสดงผล
- เปิด/ปิดใช้งาน (Toggle)

---

## ⚙️ Settings Management

**URL:** `/admin/settings/`

### 1. General Settings (index.php)

**Basic:**
- Site Name
- Site Description
- Site Keywords
- Logo (upload)
- Favicon (upload)

**Contact:**
- Email
- Phone
- Address
- Line ID
- Working Hours

**Social Media:**
- Facebook
- Instagram
- Twitter
- YouTube
- TikTok
- WhatsApp

**SEO:**
- Meta Title
- Meta Description
- Google Analytics ID
- Facebook Pixel ID

**Display:**
- Items per page
- Go to top button (Toggle)
- Social icons (Toggle)

### 2. SEO Settings (seo.php)
- Meta Title
- Meta Description
- Meta Keywords
- OG Image
- Google Analytics
- Facebook Pixel

### 3. Social Settings (social.php)
- Facebook URL + Toggle
- Instagram URL + Toggle
- Twitter URL + Toggle
- Line ID + Toggle
- YouTube URL + Toggle
- TikTok URL + Toggle

---

## 🔐 Roles & Permissions

**URL:** `/admin/roles/`

### Roles (4 roles):
- Programmer (ทุกอย่าง)
- Admin (จัดการทั้งหมด)
- Editor (เนื้อหา)
- Viewer (ดูอย่างเดียว)

### Features (11 features):
1. Models
2. Categories
3. Articles
4. Article Categories
5. Users
6. Menus
7. Bookings
8. Contacts
9. Gallery
10. Homepage
11. Settings

### Permission Types (5 types):
- View (ดู)
- Create (สร้าง)
- Edit (แก้ไข)
- Delete (ลบ)
- Export (ส่งออก)

### การตั้งค่าสิทธิ์:
- Toggle แต่ละสิทธิ์ (Auto-save)
- Quick Set:
  - ✅ เปิดทั้งหมด
  - 👁️ ดูอย่างเดียว
  - ❌ ปิดทั้งหมด
  - ➕ สิทธิ์พื้นฐาน

---

## ⭐ Reviews Management

**URL:** `/admin/reviews/`

### ฟีเจอร์:

#### ข้อมูลรีวิว:
- ชื่อลูกค้า *
- Rating (1-5 ดาว) *
- เนื้อหารีวิว *
- รูปภาพลูกค้า (upload)
- ลำดับแสดงผล
- เปิด/ปิดใช้งาน

#### การกระทำ:
- เพิ่มรีวิว
- แก้ไขรีวิว
- ลบรีวิว

---

## 🗄️ ฐานข้อมูล

### ตารางทั้งหมด (18 tables):

1. **users** - ผู้ใช้ระบบ
2. **roles** - บทบาท
3. **permissions** - สิทธิ์
4. **models** - โมเดล
5. **model_images** - รูปโมเดล
6. **model_requirements** - คุณสมบัติ
7. **categories** - หมวดหมู่โมเดล
8. **articles** - บทความ
9. **article_categories** - หมวดหมู่บทความ
10. **menus** - เมนู
11. **bookings** - การจอง
12. **contacts** - ข้อความติดต่อ
13. **gallery_images** - รูปภาพ
14. **gallery_albums** - อัลบั้ม
15. **customer_reviews** - รีวิวลูกค้า
16. **homepage_sections** - ส่วนหน้าแรก
17. **settings** - การตั้งค่า
18. **activity_logs** - บันทึกการทำงาน

### Columns สำคัญ:

**homepage_sections:**
```
- id
- section_key (hero, about, services, etc.)
- title
- subtitle
- content
- background_type (color/image)
- background_color
- background_image
- background_position
- background_size
- background_repeat
- background_attachment
- left_image
- button1_text
- button1_link
- button2_text
- button2_link
- is_active
- sort_order
```

**customer_reviews:**
```
- id
- customer_name
- image
- rating (1-5)
- content
- is_active
- sort_order
- created_at
```

**gallery_images:**
```
- id
- album_id
- image_path
- thumbnail_path
- title
- description
- uploaded_by
- is_active
- sort_order
- view_count
```

---

## 🔒 Security

### CSRF Protection:
- ทุกฟอร์มมี CSRF token
- Validate ก่อนทำงาน

### SQL Injection Prevention:
- ใช้ Prepared Statements
- bind_param() ทุกครั้ง

### XSS Prevention:
- htmlspecialchars() สำหรับ output
- clean_input() สำหรับ input

### Password:
- bcrypt hashing
- password_hash() / password_verify()

### Session:
- Timeout 2 ชั่วโมง
- Remember me option
- Activity logging

---

## 🎯 การใช้งาน

### เพิ่มโมเดลใหม่:
1. Models → Add Model
2. กรอกข้อมูล (Code, Name, Category)
3. Upload รูป
4. บันทึก

### จัดการหน้าแรก:
1. Homepage → เลือก Section
2. คลิก "แก้ไข"
3. แก้ไขเนื้อหา
4. Upload รูปพื้นหลัง (ถ้าต้องการ)
5. บันทึก

### ตั้งค่าสิทธิ์:
1. Roles → เลือก Role
2. คลิก "แก้ไขสิทธิ์"
3. Toggle แต่ละฟีเจอร์
4. หรือใช้ Quick Set
5. Auto-save

---

**หมายเหตุ:** ทุกฟีเจอร์ถูกจำกัดด้วย Role & Permission system


