<?php
/**
 * เพิ่มหมวดหมู่บทความตัวอย่าง
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มหมวดหมู่บทความ</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Noto Sans Thai', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 10px; font-size: 2rem; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .info-box { background: #f0f4ff; border-left: 4px solid #667eea; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
        .btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 1.1rem; cursor: pointer; width: 100%; font-weight: 600; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .progress { background: #e0e0e0; border-radius: 20px; height: 30px; margin: 20px 0; overflow: hidden; }
        .progress-bar { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); height: 100%; width: 0%; transition: width 0.3s ease; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
        .log { background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 20px; max-height: 400px; overflow-y: auto; margin-top: 20px; }
        .log p { padding: 8px; margin: 5px 0; border-radius: 5px; }
        .log p.success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .log p.error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .log p.info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .log p.warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: bold; }
        .stat-label { font-size: 0.9rem; opacity: 0.9; margin-top: 5px; }
        .category-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; margin-top: 20px; }
        .category-item { background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 10px; padding: 15px; transition: all 0.3s ease; }
        .category-item:hover { border-color: #667eea; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2); }
        .category-name { font-weight: 600; color: #333; margin-bottom: 5px; }
        .category-slug { color: #666; font-size: 0.85rem; font-family: monospace; background: #e9ecef; padding: 3px 8px; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌟 เพิ่มหมวดหมู่บทความตัวอย่าง</h1>
        <p class="subtitle">สร้างหมวดหมู่บทความสำหรับระบบ VIBEDAYBKK</p>
        
        <div class="info-box">
            <p><strong>📋 ข้อมูลหมวดหมู่บทความที่จะเพิ่ม:</strong></p>
            <p>จำนวนหมวดหมู่: <strong>8 หมวดหมู่</strong></p>
            <p>สถานะ: <strong>เปิดใช้งานทั้งหมด</strong></p>
        </div>

        <button id="startBtn" class="btn" onclick="startSeeding()">
            <i class="fas fa-play"></i> เริ่มเพิ่มหมวดหมู่บทความ
        </button>

        <div class="progress" style="display: none;">
            <div class="progress-bar" id="progressBar">0%</div>
        </div>

        <div class="stats" id="stats" style="display: none;">
            <div class="stat-card">
                <div class="stat-number" id="totalCount">0</div>
                <div class="stat-label">หมวดหมู่ทั้งหมด</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number" id="successCount">0</div>
                <div class="stat-label">สำเร็จ</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <div class="stat-number" id="errorCount">0</div>
                <div class="stat-label">ล้มเหลว</div>
            </div>
        </div>

        <div class="log" id="log"></div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/" class="btn" style="display: inline-block; width: auto; padding: 12px 30px; text-decoration: none;">กลับหน้าแรก</a>
            <a href="seed-articles.php" class="btn" style="display: inline-block; width: auto; padding: 12px 30px; text-decoration: none; margin-left: 10px;">เพิ่มบทความ</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        const categories = [
            { name: 'ข่าวสาร', name_en: 'News', slug: 'news', description: 'ข่าวสารและอัพเดทล่าสุด', icon: 'fa-newspaper', color: '#3B82F6' },
            { name: 'แฟชั่น', name_en: 'Fashion', slug: 'fashion', description: 'เทรนด์แฟชั่นและสไตล์', icon: 'fa-shirt', color: '#EC4899' },
            { name: 'ไลฟ์สไตล์', name_en: 'Lifestyle', slug: 'lifestyle', description: 'การใช้ชีวิตและกิจกรรม', icon: 'fa-heart', color: '#F59E0B' },
            { name: 'ความสวยความงาม', name_en: 'Beauty', slug: 'beauty', description: 'เคล็ดลับความสวยและการดูแลตัวเอง', icon: 'fa-spa', color: '#8B5CF6' },
            { name: 'การถ่ายภาพ', name_en: 'Photography', slug: 'photography', description: 'เทคนิคและเคล็ดลับการถ่ายภาพ', icon: 'fa-camera', color: '#10B981' },
            { name: 'อีเวนต์', name_en: 'Events', slug: 'events', description: 'งานอีเวนต์และกิจกรรมต่างๆ', icon: 'fa-calendar', color: '#EF4444' },
            { name: 'เบื้องหลัง', name_en: 'Behind The Scenes', slug: 'behind-the-scenes', description: 'เบื้องหลังการทำงานและเรื่องราว', icon: 'fa-film', color: '#6366F1' },
            { name: 'เคล็ดลับ', name_en: 'Tips & Tricks', slug: 'tips-tricks', description: 'เคล็ดลับและคำแนะนำที่เป็นประโยชน์', icon: 'fa-lightbulb', color: '#FBBF24' }
        ];

        let successCount = 0;
        let errorCount = 0;

        function log(message, type = 'info') {
            const logDiv = document.getElementById('log');
            const p = document.createElement('p');
            p.className = type;
            p.textContent = message;
            logDiv.appendChild(p);
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function updateStats() {
            document.getElementById('totalCount').textContent = categories.length;
            document.getElementById('successCount').textContent = successCount;
            document.getElementById('errorCount').textContent = errorCount;
        }

        function updateProgress(current, total) {
            const percent = Math.round((current / total) * 100);
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = percent + '%';
            progressBar.textContent = percent + '%';
        }

        async function startSeeding() {
            const startBtn = document.getElementById('startBtn');
            const progressDiv = document.querySelector('.progress');
            const statsDiv = document.getElementById('stats');
            const logDiv = document.getElementById('log');

            startBtn.disabled = true;
            startBtn.textContent = '⏳ กำลังเพิ่มหมวดหมู่...';
            progressDiv.style.display = 'block';
            statsDiv.style.display = 'grid';
            logDiv.innerHTML = '';

            log('🚀 เริ่มเพิ่มหมวดหมู่บทความตัวอย่าง', 'info');
            
            // ลบหมวดหมู่เดิม
            log('⚠️ กำลังลบหมวดหมู่เดิม...', 'warning');
            try {
                const deleteResponse = await fetch('seed-article-categories-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_all' })
                });
                const deleteResult = await deleteResponse.json();
                if (deleteResult.success) {
                    log(`✅ ลบหมวดหมู่เดิมแล้ว: ${deleteResult.deleted_count} รายการ`, 'success');
                } else {
                    log(`❌ เกิดข้อผิดพลาดในการลบหมวดหมู่เดิม: ${deleteResult.error}`, 'error');
                }
            } catch (error) {
                log(`❌ เกิดข้อผิดพลาด: ${error.message}`, 'error');
            }

            // เพิ่มหมวดหมู่ใหม่
            for (let i = 0; i < categories.length; i++) {
                const category = categories[i];
                
                try {
                    const response = await fetch('seed-article-categories-api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            action: 'add',
                            category: category
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        log(`✅ [${i + 1}/${categories.length}] เพิ่มหมวดหมู่: ${category.name} (${category.name_en})`, 'success');
                        successCount++;
                    } else {
                        log(`❌ [${i + 1}/${categories.length}] เกิดข้อผิดพลาด: ${result.error}`, 'error');
                        errorCount++;
                    }
                } catch (error) {
                    log(`❌ [${i + 1}/${categories.length}] เกิดข้อผิดพลาด: ${error.message}`, 'error');
                    errorCount++;
                }

                updateProgress(i + 1, categories.length);
                updateStats();
            }

            log('🎉 เสร็จสิ้น!', 'success');
            log(`✅ สำเร็จ: ${successCount} รายการ`, 'success');
            if (errorCount > 0) {
                log(`❌ ล้มเหลว: ${errorCount} รายการ`, 'error');
            }

            startBtn.textContent = '✅ เพิ่มหมวดหมู่เสร็จสิ้น';
        }
    </script>
</body>
</html>


