<?php
/**
 * Seed Categories - สร้างหมวดหมู่โมเดลตัวอย่าง
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ข้อมูลหมวดหมู่
$categories = [
    [
        'name' => 'นางแบบ',
        'slug' => 'nang-baeb',
        'description' => 'นางแบบมืออาชีพสำหรับงานแฟชั่น โฆษณา และถ่ายภาพ',
        'icon' => 'fa-user-tie',
        'image' => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=800',
        'price_range' => '3,000-15,000 บาท',
        'sort_order' => 1,
        'status' => 'active'
    ],
    [
        'name' => 'พรีเซ็นเตอร์',
        'slug' => 'presenter',
        'description' => 'พรีเซ็นเตอร์สินค้า Brand Ambassador และงานอีเวนต์',
        'icon' => 'fa-star',
        'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=800',
        'price_range' => '5,000-20,000 บาท',
        'sort_order' => 2,
        'status' => 'active'
    ],
    [
        'name' => 'โมเดลผู้ชาย',
        'slug' => 'model-phu-chai',
        'description' => 'โมเดลผู้ชายสำหรับงานแฟชั่นและโฆษณา',
        'icon' => 'fa-male',
        'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=800',
        'price_range' => '4,000-18,000 บาท',
        'sort_order' => 3,
        'status' => 'active'
    ],
    [
        'name' => 'Kids Model',
        'slug' => 'kids-model',
        'description' => 'โมเดลเด็กสำหรับงานโฆษณาและถ่ายภาพ',
        'icon' => 'fa-child',
        'image' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=800',
        'price_range' => '3,000-12,000 บาท',
        'sort_order' => 4,
        'status' => 'active'
    ],
    [
        'name' => 'Fitness Model',
        'slug' => 'fitness-model',
        'description' => 'โมเดลฟิตเนสสำหรับแบรนด์กีฬาและสุขภาพ',
        'icon' => 'fa-dumbbell',
        'image' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800',
        'price_range' => '5,000-15,000 บาท',
        'sort_order' => 5,
        'status' => 'active'
    ],
    [
        'name' => 'Plus Size Model',
        'slug' => 'plus-size-model',
        'description' => 'โมเดล Plus Size สำหรับแบรนด์แฟชั่นทุกไซส์',
        'icon' => 'fa-heart',
        'image' => 'https://images.unsplash.com/photo-1583391733956-6c78276477e2?w=800',
        'price_range' => '4,000-15,000 บาท',
        'sort_order' => 6,
        'status' => 'active'
    ],
    [
        'name' => 'MC & พิธีกร',
        'slug' => 'mc-phithikorn',
        'description' => 'MC และพิธีกรมืออาชีพสำหรับงานอีเวนต์',
        'icon' => 'fa-microphone',
        'image' => 'https://images.unsplash.com/photo-1475483768296-6163e08872a1?w=800',
        'price_range' => '8,000-25,000 บาท',
        'sort_order' => 7,
        'status' => 'active'
    ],
    [
        'name' => 'Commercial Model',
        'slug' => 'commercial-model',
        'description' => 'โมเดลสำหรับงานโฆษณา TVC และสื่อดิจิทัล',
        'icon' => 'fa-video',
        'image' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=800',
        'price_range' => '6,000-30,000 บาท',
        'sort_order' => 8,
        'status' => 'active'
    ]
];

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📁 สร้างหมวดหมู่โมเดล</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #f5576c;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .category-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .category-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
            display: block;
        }
        .category-card h3 {
            font-size: 1.3em;
            margin-bottom: 10px;
        }
        .category-card p {
            font-size: 0.9em;
            opacity: 0.9;
            line-height: 1.5;
        }
        .category-card .price {
            margin-top: 10px;
            font-weight: bold;
            padding: 8px 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: inline-block;
        }
        .btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 1.2em;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
        }
        .result {
            margin-top: 25px;
            padding: 20px;
            border-radius: 10px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: none;
        }
        .result.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
        }
        .stat-box .number {
            font-size: 3em;
            font-weight: bold;
        }
        .stat-box .label {
            font-size: 1em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-folder-plus"></i> สร้างหมวดหมู่โมเดล</h1>
        <p class="subtitle">สร้างหมวดหมู่โมเดล 8 ประเภท พร้อมข้อมูลครบถ้วน</p>
        
        <div class="stats">
            <div class="stat-box">
                <div class="number">8</div>
                <div class="label">หมวดหมู่</div>
            </div>
            <div class="stat-box">
                <div class="number" id="current-count">
                    <?php
                    $current = $conn->query("SELECT COUNT(*) as total FROM categories");
                    echo $current->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="label">ที่มีอยู่</div>
            </div>
        </div>
        
        <h2 style="color: #f5576c; margin-bottom: 20px; font-size: 1.5em;">
            <i class="fas fa-list"></i> หมวดหมู่ที่จะสร้าง:
        </h2>
        
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <div class="category-card">
                    <i class="fas <?php echo $cat['icon']; ?>"></i>
                    <h3><?php echo $cat['name']; ?></h3>
                    <p><?php echo $cat['description']; ?></p>
                    <div class="price"><?php echo $cat['price_range']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button class="btn" onclick="createCategories()">
            <i class="fas fa-magic"></i> สร้างหมวดหมู่ทั้งหมด
        </button>
        
        <div id="result" class="result"></div>
    </div>
    
    <script>
        async function createCategories() {
            const btn = document.querySelector('.btn');
            const resultEl = document.getElementById('result');
            
            btn.disabled = true;
            btn.textContent = '⏳ กำลังสร้าง...';
            resultEl.style.display = 'none';
            
            try {
                const response = await fetch('seed-categories-api.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'create_all'})
                });
                
                const result = await response.json();
                
                if (result.success) {
                    resultEl.className = 'result';
                    resultEl.innerHTML = `
                        <h3 style="margin-bottom: 10px;"><i class="fas fa-check-circle"></i> สำเร็จ!</h3>
                        <p>✅ สร้างหมวดหมู่ใหม่: <strong>${result.created}</strong> รายการ</p>
                        <p>ℹ️ มีอยู่แล้ว: <strong>${result.skipped}</strong> รายการ</p>
                        <p>📊 รวมทั้งหมด: <strong>${result.total}</strong> หมวดหมู่</p>
                        <p style="margin-top: 15px;">
                            <a href="seed-models.php" style="color: #155724; text-decoration: underline; font-weight: bold;">
                                ➡️ ไปสร้างโมเดลตัวอย่าง 100 รายการ
                            </a>
                        </p>
                    `;
                    resultEl.style.display = 'block';
                    
                    document.getElementById('current-count').textContent = result.total;
                    btn.innerHTML = '<i class="fas fa-check"></i> สร้างเสร็จแล้ว!';
                } else {
                    throw new Error(result.error);
                }
            } catch (error) {
                resultEl.className = 'result error';
                resultEl.innerHTML = `
                    <h3 style="margin-bottom: 10px;"><i class="fas fa-times-circle"></i> เกิดข้อผิดพลาด!</h3>
                    <p>${error.message}</p>
                `;
                resultEl.style.display = 'block';
                btn.textContent = 'ลองอีกครั้ง';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>

