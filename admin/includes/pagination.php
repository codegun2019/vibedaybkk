<?php
/**
 * Pagination Component
 * 
 * @param int $current_page Current page number
 * @param int $total_pages Total number of pages
 * @param string $base_url Base URL for pagination links
 * @param array $params Additional query parameters
 */
function render_pagination($current_page, $total_pages, $base_url = '', $params = []) {
    if ($total_pages <= 1) {
        return;
    }
    
    // Build query string from params
    $query_params = [];
    foreach ($params as $key => $value) {
        if ($value !== '' && $value !== null && $key !== 'page') {
            $query_params[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    $query_string = !empty($query_params) ? '&' . implode('&', $query_params) : '';
    
    // Calculate page range
    $range = 2; // Show 2 pages before and after current page
    $start = max(1, $current_page - $range);
    $end = min($total_pages, $current_page + $range);
    
    ?>
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
        <!-- Page Info -->
        <div class="text-sm text-gray-600">
            แสดงหน้า <span class="font-semibold text-gray-900"><?php echo $current_page; ?></span> 
            จาก <span class="font-semibold text-gray-900"><?php echo $total_pages; ?></span>
        </div>
        
        <!-- Pagination Buttons -->
        <nav class="flex items-center space-x-1">
            <!-- First Page -->
            <?php if ($current_page > 1): ?>
            <a href="<?php echo $base_url; ?>?page=1<?php echo $query_string; ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors">
                <i class="fas fa-angle-double-left"></i>
            </a>
            <?php endif; ?>
            
            <!-- Previous Page -->
            <?php if ($current_page > 1): ?>
            <a href="<?php echo $base_url; ?>?page=<?php echo $current_page - 1; ?><?php echo $query_string; ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors">
                <i class="fas fa-angle-left"></i>
            </a>
            <?php endif; ?>
            
            <!-- Page Numbers -->
            <?php if ($start > 1): ?>
            <span class="px-2 text-gray-400">...</span>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if ($i == $current_page): ?>
            <span class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-pink-600 border border-purple-600 rounded-lg shadow-md">
                <?php echo $i; ?>
            </span>
            <?php else: ?>
            <a href="<?php echo $base_url; ?>?page=<?php echo $i; ?><?php echo $query_string; ?>" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors">
                <?php echo $i; ?>
            </a>
            <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($end < $total_pages): ?>
            <span class="px-2 text-gray-400">...</span>
            <?php endif; ?>
            
            <!-- Next Page -->
            <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo $base_url; ?>?page=<?php echo $current_page + 1; ?><?php echo $query_string; ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors">
                <i class="fas fa-angle-right"></i>
            </a>
            <?php endif; ?>
            
            <!-- Last Page -->
            <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo $base_url; ?>?page=<?php echo $total_pages; ?><?php echo $query_string; ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors">
                <i class="fas fa-angle-double-right"></i>
            </a>
            <?php endif; ?>
        </nav>
    </div>
    <?php
}
?>

