<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทำความสะอาด Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans Thai', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            text-align: center;
        }
        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            margin: 10px;
            transition: all 0.3s ease;
            font-family: 'Noto Sans Thai', sans-serif;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .warning {
            color: #f5576c;
        }
        ul {
            text-align: left;
            margin: 20px 0;
            list-style-position: inside;
        }
        li {
            padding: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🗑️</div>
        <h1>ทำความสะอาด Gallery</h1>
        <p class="subtitle">ลบรูปภาพตัวอย่างออกจากฐานข้อมูล</p>
        
        <div class="card">
            <h3>⚠️ คำเตือน</h3>
            <p>การดำเนินการนี้จะลบรูปภาพต่อไปนี้:</p>
            <ul>
                <li>รูปภาพจาก Unsplash</li>
                <li>รูปภาพ Placeholder</li>
                <li>รูปภาพตัวอย่าง (nature-photo, beauty, workshop, skincare)</li>
            </ul>
            <p class="warning"><strong>หมายเหตุ:</strong> รูปภาพที่อัปโหลดจริงจะไม่ถูกลบ</p>
        </div>
        
        <button class="btn" onclick="cleanGallery()">🧹 ลบข้อมูลตัวอย่าง</button>
        <br>
        <button class="btn btn-secondary" onclick="window.location.href='admin/gallery/'">📸 ไปที่ Admin Gallery</button>
        <br>
        <button class="btn btn-success" onclick="window.location.href='gallery.php'">👁️ ดูหน้า Gallery</button>
    </div>

    <script>
        function cleanGallery() {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'ต้องการลบข้อมูลตัวอย่างออกจากฐานข้อมูล',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#f5576c',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // แสดง loading
                    Swal.fire({
                        title: 'กำลังดำเนินการ...',
                        text: 'กรุณารอสักครู่',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // เรียก API
                    fetch('delete-sample-gallery-data.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#667eea'
                                }).then(() => {
                                    window.location.href = 'gallery.php';
                                });
                            } else {
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#f5576c'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถเชื่อมต่อได้: ' + error,
                                icon: 'error',
                                confirmButtonColor: '#f5576c'
                            });
                        });
                }
            });
        }
    </script>
</body>
</html>
