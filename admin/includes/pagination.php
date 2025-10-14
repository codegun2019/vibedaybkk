<?php
/**
 * Pagination Component
 * ใช้สำหรับแสดง pagination ในทุกหน้า
 * 
 * Usage:
 * include 'includes/pagination.php';
 * render_pagination($current_page, $total_pages, $base_url);
 */

function render_pagination($current_page, $total_pages, $base_url = '') {
    if ($total_pages <= 1) return;
    
    // Parse existing query string
    $query_params = $_GET;
    unset($query_params['page']); // Remove page param
    
    $query_string = http_build_query($query_params);
    $separator = empty($query_string) ? '?' : '&';
    
    if (empty($base_url)) {
        $base_url = $_SERVER['PHP_SELF'] . ($query_string ? '?' . $query_string : '');
    }
    
    echo '<div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">';
    echo '<div class="text-sm text-gray-600">';
    echo 'หน้า ' . $current_page . ' จาก ' . $total_pages;
    echo '</div>';
    echo '<div class="flex space-x-2">';
    
    // Previous button
    if ($current_page > 1) {
        $prev_url = $base_url . $separator . 'page=' . ($current_page - 1);
        echo '<a href="' . $prev_url . '" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors duration-200">';
        echo '<i class="fas fa-chevron-left mr-2"></i>ก่อนหน้า';
        echo '</a>';
    } else {
        echo '<span class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">';
        echo '<i class="fas fa-chevron-left mr-2"></i>ก่อนหน้า';
        echo '</span>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    if ($start > 1) {
        $first_url = $base_url . $separator . 'page=1';
        echo '<a href="' . $first_url . '" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors duration-200">1</a>';
        if ($start > 2) {
            echo '<span class="inline-flex items-center justify-center w-10 h-10 text-gray-400">...</span>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            echo '<span class="inline-flex items-center justify-center w-10 h-10 bg-red-600 text-white rounded-lg font-semibold">' . $i . '</span>';
        } else {
            $page_url = $base_url . $separator . 'page=' . $i;
            echo '<a href="' . $page_url . '" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors duration-200">' . $i . '</a>';
        }
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            echo '<span class="inline-flex items-center justify-center w-10 h-10 text-gray-400">...</span>';
        }
        $last_url = $base_url . $separator . 'page=' . $total_pages;
        echo '<a href="' . $last_url . '" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors duration-200">' . $total_pages . '</a>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_url = $base_url . $separator . 'page=' . ($current_page + 1);
        echo '<a href="' . $next_url . '" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors duration-200">';
        echo 'ถัดไป<i class="fas fa-chevron-right ml-2"></i>';
        echo '</a>';
    } else {
        echo '<span class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">';
        echo 'ถัดไป<i class="fas fa-chevron-right ml-2"></i>';
        echo '</span>';
    }
    
    echo '</div>';
    echo '</div>';
}

