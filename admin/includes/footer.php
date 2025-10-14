        </div>
        <!-- End Content Wrapper -->
        
        <!-- Footer -->
        <footer class="text-center py-4 mt-5 border-top">
            <small class="text-muted">
                &copy; <?php echo date('Y'); ?> VIBEDAYBKK. All rights reserved. 
                | Version 1.0.0
                | <i class="fas fa-clock"></i> <?php echo date('H:i:s'); ?>
            </small>
        </footer>
    </div>
    <!-- End Main Content -->
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS (optional) -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Common Scripts -->
    <script>
        // Sidebar toggle for mobile
        $('#sidebar-toggle').click(function() {
            $('#sidebar').toggleClass('show');
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // DataTables default settings
        if ($.fn.DataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    "decimal": "",
                    "emptyTable": "ไม่มีข้อมูลในตาราง",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "แสดง _MENU_ รายการ",
                    "loadingRecords": "กำลังโหลด...",
                    "processing": "กำลังประมวลผล...",
                    "search": "ค้นหา:",
                    "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });
        }
        
        // Confirm delete
        $('.btn-delete').click(function(e) {
            if (!confirm('ต้องการลบรายการนี้?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    
    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html>

