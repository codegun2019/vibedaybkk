<?php
/**
 * Seed Models - เพิ่มข้อมูลโมเดลตัวอย่าง 100 รายการ
 */

define('VIBEDAYBKK_ADMIN', true);
require_once 'includes/config.php';

// ข้อมูลตัวอย่างสำหรับสร้างโมเดล
$first_names = [
    'กมลวรรณ', 'จิราพร', 'ชลธิชา', 'ญาณิศา', 'ฐิติมา', 'ณัฐธิดา', 'ดารารัตน์', 'ทิพวรรณ', 'นภัสสร', 'นันทิดา',
    'ปทุมพร', 'พรรณวษา', 'พัชรินทร์', 'ภัทรวดี', 'ภาวิณี', 'มัลลิกา', 'รมิดา', 'วรรณภา', 'ศิรินทิพย์', 'สิริกร',
    'สุภาพร', 'อรพรรณ', 'อัญชลี', 'เบญจมาศ', 'เมธาวี', 'แพรวพรรณ', 'โชติกา', 'ไพลิน', 'กนกวรรณ', 'จิรัชยา',
    'ชนิดา', 'ญาดา', 'ฐานิดา', 'ณัฐกานต์', 'ดวงกมล', 'ทักษพร', 'นภาพร', 'นิภาพร', 'ปาลิตา', 'พรพิมล',
    'พัทธนันท์', 'ภัทรภร', 'ภาวิดา', 'มนัสนันท์', 'รัตนาภรณ์', 'วรางคณา', 'ศิริพร', 'สิรีธร', 'สุพัตรา', 'อรทัย',
    // ชื่อผู้ชาย
    'ธนากร', 'ธีรภัทร', 'นพดล', 'ปิยะ', 'พงศกร', 'พัฒนพงศ์', 'ภูมิพัฒน์', 'รัชชานนท์', 'วรพล', 'ศุภกร',
    'สิทธิชัย', 'อนุชา', 'อัครพล', 'เจษฎา', 'ชนาธิป', 'ณัฐพงษ์', 'ธนพล', 'ธีรพงศ์', 'ปรเมศวร์', 'พิชญุตม์',
    'ภูริณัฐ', 'วชิรวิทย์', 'ศิวกร', 'สรวิศ', 'สุรเชษฐ์', 'อภิวัฒน์', 'เกียรติศักดิ์', 'ชัยวัฒน์', 'ธนวัฒน์', 'ปฏิพล',
    'พงศธร', 'ภาณุพงศ์', 'วิศรุต', 'ศักดิ์ดา', 'สิทธิพร', 'อรรถพล', 'เมธี', 'แสงมณี', 'เดชา', 'กิตติพงศ์'
];

$last_names = [
    'สุขสวัสดิ์', 'จิตรสุภา', 'วงศ์วาน', 'ทองดี', 'สุวรรณ', 'รัตนพันธ์', 'ศรีสุข', 'บุญญฤทธิ์', 'พูลสวัสดิ์', 'ชัยวงศ์',
    'ประเสริฐสุข', 'อนันตกูล', 'วิริยะกุล', 'สุรเดชกุล', 'เจริญสุข', 'มงคลสุข', 'ธนบดีกุล', 'พัฒนาวิกกุล', 'ศิริวัฒน์', 'สมบูรณ์',
    'วรรณกุล', 'สุขเจริญ', 'ทรัพย์สมบูรณ์', 'บุญเรือง', 'ทองคำ', 'เพชรรัตน์', 'อินทร์แก้ว', 'มณีรัตน์', 'ใจดี', 'สุขใจ',
    'รุ่งเรือง', 'เจริญพร', 'พงษ์สวัสดิ์', 'วิชัยดิษฐ', 'ศิลปกุล', 'อาภรณ์', 'บุญธรรม', 'ธีระกุล', 'วิทยากร', 'สุขสันต์',
    'ปัญญา', 'เมธาวิน', 'วิสุทธิ์', 'กิตติกุล', 'อุดมพร', 'ประดิษฐ์', 'นาคสุวรรณ', 'ดวงจันทร์', 'แสงทอง', 'มหาชัย'
];

$experiences = [
    'มีประสบการณ์ด้านแฟชั่นโชว์มากกว่า 5 ปี ทำงานกับแบรนด์ชั้นนำทั้งในและต่างประเทศ',
    'เคยเป็นนางแบบงานแฟชั่นวีค กรุงเทพฯ และมีลูกค้าประจำหลายแบรนด์',
    'มีประสบการณ์ถ่ายแบบสำหรับนิตยสารแฟชั่น และร่วมงานกับช่างภาพชื่อดัง',
    'ทำงานด้านพรีเซ็นเตอร์สินค้า, MC งานอีเวนต์ และถ่ายโฆษณาออนไลน์',
    'เคยเป็นนางแบบประจำห้างสรรพสินค้าชั้นนำ และมีผลงานโฆษณาทีวี',
    'มีประสบการณ์ถ่ายแบบเสื้อผ้า เครื่องสำอาง และสินค้าไลฟ์สไตล์',
    'ทำงานด้านโมเดล MC และพิธีกร มีประสบการณ์มากกว่า 3 ปี',
    'เคยร่วมงานกับแบรนด์เครื่องสำอางระดับโลก และมีผลงาน TVC หลายเรื่อง',
    'มีประสบการณ์เดินแบบในงานแฟชั่นโชว์และถ่ายแบบหนังสือแฟชั่น',
    'ทำงานด้านนางแบบออนไลน์ พรีเซ็นเตอร์สินค้า และ Brand Ambassador',
    'เคยเป็นนางประกวด และมีประสบการณ์ในวงการแฟชั่นมากกว่า 4 ปี',
    'มีประสบการณ์ถ่ายโฆษณา TVC, โปสเตอร์ และแคมเปญโซเชียลมีเดีย',
    'ทำงานด้าน Fitness Model และมีผลงาน Cover นิตยสารสุขภาพ',
    'เคยร่วมงานกับแบรนด์กีฬา แฟชั่น และเครื่องสำอางชั้นนำ',
    'มีประสบการณ์เดินแบบชุดว่ายน้ำ ชุดชั้นใน และแฟชั่น Casual'
];

$portfolios = [
    'แคมเปญ Siam Paragon Fashion Week 2024, Vogue Thailand Magazine Cover, Central Fashion Show',
    'TVC Samsung Galaxy, Billboard AIS 5G, Fashion Show Terminal 21',
    'L\'Oreal Paris Campaign, ELLE Magazine, CentralWorld New Year Countdown',
    'Lazada Fashion Campaign, Shopee Beauty Ambassador, Instagram @vibedaybkk Featured Model',
    'Nike Thailand Running Campaign, The Mall Fashion Week, Harper\'s Bazaar Editorial',
    'Uniqlo Autumn Collection, Zara Thailand Campaign, Cosmopolitan Magazine',
    'MAC Cosmetics TVC, Sephora Thailand Ambassador, Marie Claire Cover',
    'Adidas Originals Thailand, EmQuartier Fashion Show, GQ Magazine Feature',
    'Estee Lauder Campaign, Central Embassy Grand Opening, Vogue Beauty Editorial',
    'H&M Thailand Collection, Mega Bangna Fashion Show, Elle Beauty Feature',
    'CHANEL Beauty Event, Siam Discovery Fashion Show, Numéro Thailand',
    'DIOR Thailand Campaign, IconSiam Opening Event, Harper\'s Bazaar Feature',
    'Louis Vuitton Exhibition, The Emporium Fashion Week, Wallpaper Magazine',
    'Gucci Thailand Event, Gaysorn Village Fashion Show, Vogue Thailand Feature',
    'Prada Beauty Launch, Central World Fashion Week, GQ Style Editorial'
];

$descriptions = [
    'โมเดลมืออาชีพด้านแฟชั่นและโฆษณา มีความเชี่ยวชาญด้านการถ่ายภาพทุกประเภท',
    'นางแบบประจำห้างดัง มีบุคลิกโดดเด่น เหมาะกับงานแฟชั่นและโฆษณา',
    'โมเดลมืออาชีพ มีประสบการณ์สูง พร้อมให้บริการงานคุณภาพ',
    'นางแบบ-พรีเซ็นเตอร์ บุคลิกดี ใบหน้าสวย รูปร่างดี เหมาะกับทุกงาน',
    'โมเดลด้านแฟชั่น มีผลงานมากมาย ประสบการณ์การทำงานระดับสากล',
    'นางแบบออนไลน์ มีความเชี่ยวชาญด้านการถ่ายภาพสินค้า E-commerce',
    'โมเดล MC พิธีกร มีประสบการณ์งานอีเวนต์และแฟชั่นโชว์',
    'นางแบบโฆษณา มีใบหน้าสวย รูปร่างดี เหมาะกับงาน TVC และแคมเปญ',
    'โมเดลแฟชั่น มีรสนิยมดี เข้าใจเทรนด์ พร้อมสร้างสรรค์ผลงาน',
    'นางแบบมืออาชีพ บุคลิกดี สามารถปรับตัวได้ดีกับทุกงาน'
];

// ฟังก์ชันสร้าง slug
function createSlug($text, $id) {
    // แปลงเป็นตัวพิมพ์เล็ก
    $text = strtolower($text);
    // แทนที่อักขระพิเศษด้วยขีด
    $text = preg_replace('/[^a-z0-9ก-๙]+/', '-', $text);
    // ลบขีดซ้ำซ้อน
    $text = preg_replace('/-+/', '-', $text);
    // ลบขีดหน้าหลัง
    $text = trim($text, '-');
    // เพิ่ม ID
    return $text . '-' . $id;
}

// ตรวจสอบว่ามี categories หรือยัง
$categories_check = $conn->query("SELECT COUNT(*) as total FROM categories WHERE status = 'active'");
$cat_count = $categories_check->fetch_assoc()['total'];

if ($cat_count == 0) {
    die("⚠️ ไม่พบหมวดหมู่โมเดล กรุณาสร้างหมวดหมู่ก่อน!");
}

// ดึงหมวดหมู่ทั้งหมด
$categories = [];
$cat_result = $conn->query("SELECT id, name FROM categories WHERE status = 'active'");
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎭 เพิ่มโมเดลตัวอย่าง 100 รายการ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
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
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            opacity: 0.9;
        }
        .options {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .option-group {
            margin-bottom: 20px;
        }
        .option-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .option-group input, .option-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        #progress {
            display: none;
            margin-top: 25px;
        }
        .progress-bar {
            background: #e0e0e0;
            height: 30px;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            width: 0%;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        #log {
            margin-top: 20px;
            background: #1e1e1e;
            color: #0f0;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            max-height: 400px;
            overflow-y: auto;
            font-size: 0.9em;
            line-height: 1.6;
        }
        .log-success { color: #0f0; }
        .log-error { color: #f00; }
        .log-info { color: #0ff; }
        .preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .preview h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
        .preview-item {
            background: white;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85em;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> เพิ่มโมเดลตัวอย่าง</h1>
        <p class="subtitle">สร้างข้อมูลโมเดลตัวอย่าง 100 รายการ พร้อม portfolio และประสบการณ์</p>
        
        <div class="stats">
            <div class="stat-box">
                <div class="number">100</div>
                <div class="label">รายการที่จะสร้าง</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo count($categories); ?></div>
                <div class="label">หมวดหมู่</div>
            </div>
            <div class="stat-box">
                <div class="number" id="current-count">
                    <?php
                    $current = $conn->query("SELECT COUNT(*) as total FROM models");
                    echo $current->fetch_assoc()['total'];
                    ?>
                </div>
                <div class="label">โมเดลปัจจุบัน</div>
            </div>
        </div>
        
        <div class="options">
            <h3 style="margin-bottom: 20px; color: #667eea;"><i class="fas fa-sliders-h"></i> ตัวเลือก</h3>
            
            <div class="option-group">
                <label>จำนวนโมเดลที่ต้องการสร้าง:</label>
                <input type="number" id="count" value="100" min="1" max="500">
            </div>
            
            <div class="option-group">
                <label>ช่วงราคา (บาท):</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <input type="number" id="price-min" value="3000" placeholder="ราคาต่ำสุด">
                    <input type="number" id="price-max" value="15000" placeholder="ราคาสูงสุด">
                </div>
            </div>
            
            <div class="option-group">
                <label>
                    <input type="checkbox" id="use-placeholder" checked style="width: auto; margin-right: 8px;">
                    ใช้รูป Placeholder (UI Faces)
                </label>
            </div>
            
            <div class="option-group">
                <label>
                    <input type="checkbox" id="clear-first" style="width: auto; margin-right: 8px;">
                    ลบโมเดลเดิมทั้งหมดก่อน (⚠️ ระวัง!)
                </label>
            </div>
        </div>
        
        <button class="btn" onclick="generateModels()">
            <i class="fas fa-magic"></i> สร้างโมเดลตัวอย่าง
        </button>
        
        <div id="progress">
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill">0%</div>
            </div>
            <div id="log"></div>
        </div>
        
        <div class="preview" id="preview" style="display: none;">
            <h3><i class="fas fa-eye"></i> ตัวอย่างข้อมูลที่จะสร้าง</h3>
            <div class="preview-grid" id="preview-grid"></div>
        </div>
    </div>
    
    <script>
        // ข้อมูลจาก PHP
        const categories = <?php echo json_encode($categories); ?>;
        const firstNames = <?php echo json_encode($first_names); ?>;
        const lastNames = <?php echo json_encode($last_names); ?>;
        const experiences = <?php echo json_encode($experiences); ?>;
        const portfolios = <?php echo json_encode($portfolios); ?>;
        const descriptions = <?php echo json_encode($descriptions); ?>;
        
        function log(message, type = 'info') {
            const logEl = document.getElementById('log');
            const className = 'log-' + type;
            logEl.innerHTML += `<div class="${className}">${message}</div>`;
            logEl.scrollTop = logEl.scrollHeight;
        }
        
        function updateProgress(current, total) {
            const percent = Math.round((current / total) * 100);
            const fill = document.getElementById('progress-fill');
            fill.style.width = percent + '%';
            fill.textContent = percent + '%';
        }
        
        function random(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
        
        function randomItem(array) {
            return array[random(0, array.length - 1)];
        }
        
        function generateBirthDate() {
            const age = random(18, 35);
            const year = new Date().getFullYear() - age;
            const month = String(random(1, 12)).padStart(2, '0');
            const day = String(random(1, 28)).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        async function generateModels() {
            const count = parseInt(document.getElementById('count').value);
            const priceMin = parseInt(document.getElementById('price-min').value);
            const priceMax = parseInt(document.getElementById('price-max').value);
            const usePlaceholder = document.getElementById('use-placeholder').checked;
            const clearFirst = document.getElementById('clear-first').checked;
            
            if (count < 1 || count > 500) {
                alert('กรุณาระบุจำนวน 1-500 รายการ');
                return;
            }
            
            const btn = document.querySelector('.btn');
            btn.disabled = true;
            btn.textContent = '⏳ กำลังสร้าง...';
            
            document.getElementById('progress').style.display = 'block';
            document.getElementById('log').innerHTML = '';
            
            log('🚀 เริ่มสร้างโมเดลตัวอย่าง ' + count + ' รายการ', 'info');
            
            // ลบข้อมูลเดิมถ้าเลือก
            if (clearFirst) {
                log('⚠️ กำลังลบโมเดลเดิม...', 'info');
                const delResponse = await fetch('seed-models-api.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'clear'})
                });
                const delResult = await delResponse.json();
                log('✅ ลบโมเดลเดิมแล้ว: ' + delResult.deleted + ' รายการ', 'success');
            }
            
            let success = 0;
            let errors = 0;
            
            for (let i = 1; i <= count; i++) {
                const firstName = randomItem(firstNames);
                const lastName = randomItem(lastNames);
                const name = firstName + ' ' + lastName;
                const gender = i <= 70 ? 'female' : 'male'; // 70% หญิง, 30% ชาย
                
                const modelData = {
                    action: 'create',
                    category_id: randomItem(categories).id,
                    name: name,
                    price: random(priceMin, priceMax),
                    height: random(160, 185),
                    weight: random(45, 75),
                    birth_date: generateBirthDate(),
                    experience: randomItem(experiences),
                    portfolio: randomItem(portfolios),
                    description: randomItem(descriptions),
                    featured_image: usePlaceholder ? `https://i.pravatar.cc/400?img=${random(1, 70)}` : '',
                    status: 'available'
                };
                
                try {
                    const response = await fetch('seed-models-api.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(modelData)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        success++;
                        log(`✅ [${i}/${count}] สร้าง: ${name} (${modelData.height}cm, ${modelData.price}฿)`, 'success');
                    } else {
                        errors++;
                        log(`❌ [${i}/${count}] ล้มเหลว: ${result.error}`, 'error');
                    }
                } catch (error) {
                    errors++;
                    log(`❌ [${i}/${count}] เกิดข้อผิดพลาด: ${error.message}`, 'error');
                }
                
                updateProgress(i, count);
                
                // หน่วงเวลาเล็กน้อย
                if (i % 10 === 0) {
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
            }
            
            log('', 'info');
            log('🎉 สร้างเสร็จสิ้น!', 'success');
            log(`✅ สำเร็จ: ${success} รายการ`, 'success');
            if (errors > 0) {
                log(`❌ ล้มเหลว: ${errors} รายการ`, 'error');
            }
            
            // อัพเดทจำนวนโมเดลปัจจุบัน
            const countResponse = await fetch('seed-models-api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'count'})
            });
            const countResult = await countResponse.json();
            document.getElementById('current-count').textContent = countResult.total;
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> สร้างเสร็จแล้ว!';
            
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-magic"></i> สร้างโมเดลตัวอย่าง';
            }, 3000);
        }
    </script>
</body>
</html>

