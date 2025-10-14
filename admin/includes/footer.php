            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
    
    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-admin-dark to-admin-darker text-white transform -translate-x-full transition-transform duration-300 z-50 md:hidden overflow-y-auto">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <i class="fas fa-star text-3xl text-red-primary"></i>
                    <h2 class="text-lg font-bold mt-2">VIBEDAYBKK</h2>
                </div>
                <button id="close-sidebar" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <nav class="p-4">
            <a href="<?php echo ADMIN_URL; ?>/index.php" class="flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white">
                <i class="fas fa-tachometer-alt w-6"></i><span>Dashboard</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/models/" class="flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white">
                <i class="fas fa-users w-6"></i><span>จัดการโมเดล</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/categories/" class="flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white">
                <i class="fas fa-th-large w-6"></i><span>จัดการหมวดหมู่</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/articles/" class="flex items-center px-4 py-3 mb-2 rounded-lg text-gray-300 hover:bg-red-primary/20 hover:text-white">
                <i class="fas fa-newspaper w-6"></i><span>จัดการบทความ</span>
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
