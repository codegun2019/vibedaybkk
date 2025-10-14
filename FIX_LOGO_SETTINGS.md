# 🔧 วิธีแก้ปัญหา: บันทึก Logo และ Favicon ไม่ได้

## ⚠️ ปัญหา:
เข้าหน้า Admin → Settings → อัพโหลด Logo/Favicon → บันทึกไม่สำเร็จ

## 🎯 สาเหตุ:
Database ยังไม่มี settings สำหรับ `logo_type`, `logo_text`, `logo_image`, `favicon`

---

## ✅ วิธีแก้ไข (เลือก 1 วิธี):

### วิธีที่ 1: ใช้ไฟล์ PHP (ง่ายที่สุด) ⭐

1. **เปิดเบราว์เซอร์ไปที่:**
   ```
   http://localhost/vibedaybkk/update-settings.php
   ```

2. **ไฟล์นี้จะ:**
   - ✅ ตรวจสอบว่ามี settings หรือยัง
   - ✅ เพิ่ม settings ใหม่อัตโนมัติ
   - ✅ แสดงผลลัพธ์

3. **เมื่อเห็นข้อความ "✅ อัพเดทสำเร็จ!"**
   - กลับไปที่ Admin → Settings
   - Refresh หน้า (F5)
   - ตอนนี้จะบันทึก Logo และ Favicon ได้แล้ว! 🎉

---

### วิธีที่ 2: ใช้ phpMyAdmin

1. **เปิด phpMyAdmin**
   - ไปที่: `http://localhost/phpMyAdmin`

2. **เลือกฐานข้อมูล** `vibedaybkk`

3. **คลิกแท็บ "SQL"**

4. **Copy SQL นี้และ Paste:**
   ```sql
   INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description) VALUES 
   ('logo_type', 'text', 'text', 'ประเภทโลโก้ (text, image)'),
   ('logo_text', 'VIBEDAYBKK', 'text', 'ข้อความโลโก้'),
   ('logo_image', '', 'text', 'รูปภาพโลโก้'),
   ('favicon', '', 'text', 'Favicon'),
   ('facebook_url', '', 'text', 'Facebook URL'),
   ('instagram_url', '', 'text', 'Instagram URL'),
   ('twitter_url', '', 'text', 'Twitter/X URL');
   ```

5. **คลิก "Go"**

6. **ตรวจสอบ:**
   - คลิกตาราง `settings`
   - ดูว่ามี `logo_type`, `logo_text`, `logo_image`, `favicon` หรือยัง

7. **กลับไป Admin → Settings**
   - Refresh หน้า (F5)
   - ตอนนี้จะใช้งานได้แล้ว!

---

### วิธีที่ 3: ใช้ Command Line

```bash
mysql -u root -proot vibedaybkk < C:/MAMP/htdocs/vibedaybkk/update-logo-settings.sql
```

---

## 🎯 แนะนำ: ใช้วิธีที่ 1 (ง่ายที่สุด)

**เปิด URL นี้:**
👉 `http://localhost/vibedaybkk/update-settings.php`

จะเพิ่ม settings อัตโนมัติให้!

---

## ✅ หลังจากแก้ไขแล้ว:

1. ไปที่ `http://localhost/vibedaybkk/admin/settings/`
2. จะเห็นส่วน **"โลโก้และไอคอน"** ด้านบน
3. สามารถ:
   - ✅ เลือกประเภทโลโก้ (ข้อความ/รูปภาพ)
   - ✅ แก้ไขข้อความโลโก้
   - ✅ อัพโหลดรูปโลโก้
   - ✅ อัพโหลด Favicon
4. คลิก "บันทึก"
5. เปิดหน้าบ้าน → จะเห็นโลโก้และ favicon ใหม่! 🎉

---

## 🔍 ตรวจสอบว่าทำสำเร็จหรือยัง:

เปิด phpMyAdmin:
1. เลือกฐานข้อมูล `vibedaybkk`
2. คลิกตาราง `settings`
3. ดูว่ามี rows เหล่านี้หรือยัง:
   - `logo_type`
   - `logo_text`
   - `logo_image`
   - `favicon`

ถ้ามี = ✅ แก้ไขเรียบร้อย  
ถ้าไม่มี = ⏳ รัน update-settings.php อีกครั้ง

---

**เปิด URL นี้เลยครับ:**
🔗 `http://localhost/vibedaybkk/update-settings.php`

