<?php
/**
 * Homepage Management Functions
 * ฟังก์ชันสำหรับจัดการเนื้อหาหน้าแรก
 */

// Get section icon
function get_section_icon($section_type) {
    $icons = [
        'hero' => 'home',
        'about' => 'info-circle',
        'services' => 'cogs',
        'how_to_book' => 'list-ol',
        'reviews' => 'star',
        'contact' => 'envelope',
        'gallery' => 'images',
        'testimonials' => 'quote-left',
        'cta' => 'bullhorn',
        'stats' => 'chart-bar',
        'features' => 'star'
    ];
    return $icons[$section_type] ?? 'square';
}

// Get homepage section data
function get_homepage_section($section_key) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM homepage_sections WHERE section_key = ? AND is_active = 1");
    $stmt->bind_param('s', $section_key);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();
    $stmt->close();
    
    return $section;
}

// Get section content (deprecated - use get_homepage_section instead)
function get_section_content($section_key, $content_type = null) {
    // This function is deprecated since homepage_content table is removed
    return [];
}

// Get section columns
function get_section_columns($section_key) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM homepage_columns WHERE section_key = ? AND is_active = 1 ORDER BY sort_order");
    $stmt->bind_param('s', $section_key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    $stmt->close();
    
    return $columns;
}

// Get customer reviews
function get_customer_reviews($limit = null, $featured_only = false) {
    global $conn;
    
    $sql = "SELECT * FROM customer_reviews WHERE is_active = 1";
    
    if ($featured_only) {
        $sql .= " AND is_featured = 1";
    }
    
    $sql .= " ORDER BY sort_order";
    
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    
    $stmt = $conn->prepare($sql);
    if ($limit) {
        $stmt->bind_param('i', $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
    
    return $reviews;
}

// Get section background style
function get_section_background_style($section) {
    $style = '';
    
    if ($section['background_type'] === 'image' && $section['background_image']) {
        $style .= "background-image: url('{$section['background_image']}');";
        $style .= "background-position: {$section['background_position']};";
        $style .= "background-size: {$section['background_size']};";
        $style .= "background-repeat: {$section['background_repeat']};";
        $style .= "background-attachment: {$section['background_attachment']};";
    } elseif ($section['background_type'] === 'color' && $section['background_color']) {
        $style .= "background-color: {$section['background_color']};";
    } elseif ($section['background_type'] === 'gradient' && $section['background_color']) {
        $style .= "background: linear-gradient(135deg, {$section['background_color']}, {$section['text_color']});";
    }
    
    if ($section['overlay_color'] && $section['overlay_opacity']) {
        $overlay_rgba = hex_to_rgba($section['overlay_color'], $section['overlay_opacity']);
        $style .= "position: relative;";
        $style .= "&::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: {$overlay_rgba}; z-index: 1; }";
    }
    
    return $style;
}

// Convert hex to rgba
function hex_to_rgba($hex, $opacity = 1) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgba($r, $g, $b, $opacity)";
}

// Save section content
function save_section_content($section_key, $content_type, $title, $content, $image_path = null, $image_alt = null, $link_url = null, $link_text = null, $sort_order = 0) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO homepage_content 
        (section_key, content_type, title, content, image_path, image_alt, link_url, link_text, sort_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param('ssssssssi', $section_key, $content_type, $title, $content, $image_path, $image_alt, $link_url, $link_text, $sort_order);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Update section content
function update_section_content($id, $title, $content, $image_path = null, $image_alt = null, $link_url = null, $link_text = null, $sort_order = 0) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE homepage_content 
        SET title = ?, content = ?, image_path = ?, image_alt = ?, link_url = ?, link_text = ?, sort_order = ?
        WHERE id = ?
    ");
    
    $stmt->bind_param('ssssssii', $title, $content, $image_path, $image_alt, $link_url, $link_text, $sort_order, $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Delete section content
function delete_section_content($id) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM homepage_content WHERE id = ?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Save section column
function save_section_column($section_key, $column_index, $column_title, $column_content, $column_image = null, $column_link = null, $column_class = null, $sort_order = 0) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO homepage_columns 
        (section_key, column_index, column_title, column_content, column_image, column_link, column_class, sort_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param('sisssssi', $section_key, $column_index, $column_title, $column_content, $column_image, $column_link, $column_class, $sort_order);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Update section column
function update_section_column($id, $column_title, $column_content, $column_image = null, $column_link = null, $column_class = null, $sort_order = 0) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE homepage_columns 
        SET column_title = ?, column_content = ?, column_image = ?, column_link = ?, column_class = ?, sort_order = ?
        WHERE id = ?
    ");
    
    $stmt->bind_param('sssssi', $column_title, $column_content, $column_image, $column_link, $column_class, $sort_order, $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Delete section column
function delete_section_column($id) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM homepage_columns WHERE id = ?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Save customer review
function save_customer_review($customer_name, $rating, $review_text, $customer_image = null, $review_image = null, $service_type = null, $is_featured = 0, $sort_order = 0) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO customer_reviews 
        (customer_name, customer_image, rating, review_text, review_image, service_type, is_featured, sort_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param('ssisssii', $customer_name, $customer_image, $rating, $review_text, $review_image, $service_type, $is_featured, $sort_order);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Update customer review
function update_customer_review($id, $customer_name, $rating, $review_text, $customer_image = null, $review_image = null, $service_type = null, $is_featured = 0, $sort_order = 0) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE customer_reviews 
        SET customer_name = ?, customer_image = ?, rating = ?, review_text = ?, review_image = ?, service_type = ?, is_featured = ?, sort_order = ?
        WHERE id = ?
    ");
    
    $stmt->bind_param('ssisssii', $customer_name, $customer_image, $rating, $review_text, $review_image, $service_type, $is_featured, $sort_order, $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Delete customer review
function delete_customer_review($id) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM customer_reviews WHERE id = ?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Update section background
function update_section_background($section_key, $background_type, $background_color = null, $background_image = null, $background_position = 'center', $background_size = 'cover', $background_repeat = 'no-repeat', $background_attachment = 'scroll', $overlay_color = null, $overlay_opacity = 0.5) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE homepage_sections 
        SET background_type = ?, background_color = ?, background_image = ?, background_position = ?, 
            background_size = ?, background_repeat = ?, background_attachment = ?, 
            overlay_color = ?, overlay_opacity = ?
        WHERE section_key = ?
    ");
    
    $stmt->bind_param('sssssssds', $background_type, $background_color, $background_image, $background_position, $background_size, $background_repeat, $background_attachment, $overlay_opacity, $section_key);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Get section by key
function get_section_by_key($section_key) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM homepage_sections WHERE section_key = ?");
    $stmt->bind_param('s', $section_key);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();
    $stmt->close();
    
    return $section;
}

// Get all sections
function get_all_sections($active_only = true) {
    global $conn;
    
    $sql = "SELECT * FROM homepage_sections";
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY sort_order";
    
    $result = $conn->query($sql);
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    return $sections;
}
?>


