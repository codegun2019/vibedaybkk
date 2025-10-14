            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
    
    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-slate-800 to-slate-900 text-white transform -translate-x-full transition-transform duration-300 z-50 lg:hidden overflow-y-auto">
        <div class="p-6 border-b border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <i class="fas fa-star text-3xl text-red-500"></i>
                    <h2 class="text-lg font-bold mt-2">VIBEDAYBKK</h2>
                </div>
                <button id="close-sidebar" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <nav class="p-4 space-y-1">
            <a href="<?php echo ADMIN_URL; ?>/index.php" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-tachometer-alt w-5 mr-3"></i><span>Dashboard</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/models/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-users w-5 mr-3"></i><span>จัดการโมเดล</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/categories/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-th-large w-5 mr-3"></i><span>จัดการหมวดหมู่</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/articles/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-newspaper w-5 mr-3"></i><span>จัดการบทความ</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/menus/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-bars w-5 mr-3"></i><span>จัดการเมนู</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/contacts/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-envelope w-5 mr-3"></i><span>ข้อความติดต่อ</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/bookings/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-calendar-check w-5 mr-3"></i><span>การจอง</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/settings/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-cog w-5 mr-3"></i><span>ตั้งค่าระบบ</span>
            </a>
            <?php if (is_admin()): ?>
            <a href="<?php echo ADMIN_URL; ?>/users/" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-red-600/20 hover:text-white transition-all duration-200">
                <i class="fas fa-user-shield w-5 mr-3"></i><span>จัดการผู้ใช้</span>
            </a>
            <?php endif; ?>
            
            <hr class="border-slate-700 my-4">
            
            <a href="<?php echo SITE_URL; ?>" target="_blank" class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-all duration-200">
                <i class="fas fa-external-link-alt w-5 mr-3"></i><span>ดูหน้าเว็บ</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/logout.php" onclick="return confirm('ต้องการออกจากระบบ?')" class="flex items-center px-4 py-3 rounded-lg text-red-400 hover:bg-red-600/20 hover:text-red-300 transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5 mr-3"></i><span>ออกจากระบบ</span>
            </a>
        </nav>
    </aside>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const closeSidebar = document.getElementById('close-sidebar');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                mobileSidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
            });
        }
        
        if (closeSidebar) {
            closeSidebar.addEventListener('click', () => {
                mobileSidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                mobileSidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });
        }
        
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Confirm delete
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('ต้องการลบรายการนี้?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
    
    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html>
