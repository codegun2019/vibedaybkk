<?php
/**
 * Settings Dashboard - รวมลิงก์การตั้งค่าทั้งหมด
 */

session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// ตรวจสอบการ login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'การตั้งค่า';

include '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-cog me-2"></i>การตั้งค่าระบบ</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">การตั้งค่า</li>
    </ol>

    <div class="row">
        <!-- General Settings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-sliders-h fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">การตั้งค่าทั่วไป</h5>
                        <p class="card-text text-muted">Logo, ชื่อเว็บ, ข้อมูลติดต่อ</p>
                        <a href="index.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog me-2"></i>ตั้งค่า
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appearance Settings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-palette fa-3x text-success mb-3"></i>
                        <h5 class="card-title">รูปแบบและฟอนต์</h5>
                        <p class="card-text text-muted">ฟอนต์, สี, ธีม</p>
                        <a href="appearance.php" class="btn btn-success btn-sm">
                            <i class="fas fa-paint-brush me-2"></i>ตั้งค่า
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-search fa-3x text-info mb-3"></i>
                        <h5 class="card-title">SEO & Meta Tags</h5>
                        <p class="card-text text-muted">Meta tags, OG, Twitter Card</p>
                        <a href="seo.php" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-line me-2"></i>ตั้งค่า
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Settings -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-share-alt fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">โซเชียลมีเดีย</h5>
                        <p class="card-text text-muted">Facebook, Instagram, LINE</p>
                        <a href="social.php" class="btn btn-warning btn-sm">
                            <i class="fab fa-facebook me-2"></i>ตั้งค่า
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Homepage Settings -->
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-home me-2"></i>การตั้งค่าหน้าแรก</h3>
        </div>

        <!-- Models Section -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-users fa-2x text-danger me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">โมเดลในหน้าแรก</h5>
                            <small class="text-muted">จัดการการแสดงโมเดล</small>
                        </div>
                    </div>
                    <p class="card-text">ตั้งค่าจำนวน หมวดหมู่ และการเรียงลำดับโมเดลที่แสดงในหน้าแรก</p>
                    <div class="d-flex gap-2">
                        <a href="homepage-models.php" class="btn btn-danger btn-sm">
                            <i class="fas fa-cog me-2"></i>ตั้งค่า
                        </a>
                        <a href="../../" target="_blank" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-eye me-2"></i>ดูหน้าแรก
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Model Detail Settings -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-eye fa-2x text-danger me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">
                                หน้ารายละเอียดโมเดล
                                <span class="badge bg-danger ms-2" style="font-size: 0.6em;">Admin</span>
                            </h5>
                            <small class="text-muted">ควบคุมการแสดงข้อมูล</small>
                        </div>
                    </div>
                    <p class="card-text">เปิด/ปิดฟีเจอร์และควบคุมข้อมูลที่แสดงในหน้ารายละเอียด (เฉพาะ Admin/Programmer)</p>
                    <div class="d-flex gap-2">
                        <a href="model-detail.php" class="btn btn-danger btn-sm">
                            <i class="fas fa-shield-alt me-2"></i>ตั้งค่า
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Price Display Settings -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-dollar-sign fa-2x text-warning me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">
                                การแสดงราคา
                                <span class="badge bg-danger ms-2" style="font-size: 0.6em;">Admin</span>
                            </h5>
                            <small class="text-muted">ควบคุมการแสดงราคา</small>
                        </div>
                    </div>
                    <p class="card-text">เปิด/ปิดการแสดงราคาในหน้าแรก, รายการโมเดล และรายละเอียด (เฉพาะ Admin/Programmer)</p>
                    <div class="d-flex gap-2">
                        <a href="price-display.php" class="btn btn-warning btn-sm">
                            <i class="fas fa-dollar-sign me-2"></i>ตั้งค่า
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-image fa-2x text-secondary me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">Hero Section</h5>
                            <small class="text-muted">ส่วนบนสุดของหน้าแรก</small>
                        </div>
                    </div>
                    <p class="card-text">ตั้งค่าหัวข้อ คำอธิบาย และรูปภาพพื้นหลังของ Hero Section</p>
                    <a href="#" class="btn btn-secondary btn-sm disabled">
                        <i class="fas fa-cog me-2"></i>กำลังพัฒนา
                    </a>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-briefcase fa-2x text-dark me-3"></i>
                        <div>
                            <h5 class="card-title mb-0">บริการ</h5>
                            <small class="text-muted">Section บริการ</small>
                        </div>
                    </div>
                    <p class="card-text">จัดการบริการที่แสดงในหน้าแรก พร้อมไอคอนและคำอธิบาย</p>
                    <a href="#" class="btn btn-dark btn-sm disabled">
                        <i class="fas fa-cog me-2"></i>กำลังพัฒนา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Settings -->
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-tools me-2"></i>ตั้งค่าเพิ่มเติม</h3>
        </div>

        <!-- Go to Top Button -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-up fa-2x text-primary mb-3"></i>
                    <h6 class="card-title">ปุ่มกลับขึ้นด้านบน</h6>
                    <p class="card-text text-muted small">เปิด/ปิด, สี, ตำแหน่ง</p>
                    <a href="index.php#gototop" class="btn btn-sm btn-outline-primary">ตั้งค่า</a>
                </div>
            </div>
        </div>

        <!-- Menu Management -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bars fa-2x text-success mb-3"></i>
                    <h6 class="card-title">จัดการเมนู</h6>
                    <p class="card-text text-muted small">เพิ่ม ลบ จัดเรียงเมนู</p>
                    <a href="../menus/" class="btn btn-sm btn-outline-success">จัดการ</a>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-folder fa-2x text-warning mb-3"></i>
                    <h6 class="card-title">หมวดหมู่</h6>
                    <p class="card-text text-muted small">หมวดหมู่โมเดล</p>
                    <a href="../categories/" class="btn btn-sm btn-outline-warning">จัดการ</a>
                </div>
            </div>
        </div>

        <!-- Users & Permissions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users-cog fa-2x text-danger mb-3"></i>
                    <h6 class="card-title">ผู้ใช้และสิทธิ์</h6>
                    <p class="card-text text-muted small">จัดการผู้ใช้</p>
                    <a href="../users/" class="btn btn-sm btn-outline-danger">จัดการ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

