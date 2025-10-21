/**
 * Enhanced Icon Picker for Font Awesome
 * VIBEDAYBKK Admin - Modern UI with Search
 */

// Comprehensive Font Awesome Icons
const iconCategories = {
    'Social Media': [
        'fa-facebook-f', 'fa-facebook', 'fa-instagram', 'fa-twitter', 'fa-x-twitter',
        'fa-line', 'fa-youtube', 'fa-tiktok', 'fa-linkedin', 'fa-pinterest',
        'fa-whatsapp', 'fa-telegram', 'fa-snapchat', 'fa-discord', 'fa-twitch'
    ],
    'Arrows & Navigation': [
        'fa-arrow-up', 'fa-arrow-down', 'fa-arrow-left', 'fa-arrow-right',
        'fa-arrow-circle-up', 'fa-arrow-circle-down', 'fa-arrow-circle-left', 'fa-arrow-circle-right',
        'fa-chevron-up', 'fa-chevron-down', 'fa-chevron-left', 'fa-chevron-right',
        'fa-angle-up', 'fa-angle-down', 'fa-angle-left', 'fa-angle-right',
        'fa-caret-up', 'fa-caret-down', 'fa-caret-left', 'fa-caret-right',
        'fa-long-arrow-alt-up', 'fa-long-arrow-alt-down', 'fa-long-arrow-alt-left', 'fa-long-arrow-alt-right'
    ],
    'Common Icons': [
        'fa-home', 'fa-user', 'fa-users', 'fa-user-tie', 'fa-user-graduate',
        'fa-heart', 'fa-star', 'fa-envelope', 'fa-phone', 'fa-calendar',
        'fa-clock', 'fa-map-marker-alt', 'fa-search', 'fa-cog', 'fa-edit',
        'fa-trash', 'fa-plus', 'fa-minus', 'fa-check', 'fa-times'
    ],
    'Business & Work': [
        'fa-briefcase', 'fa-building', 'fa-chart-line', 'fa-chart-bar', 'fa-chart-pie',
        'fa-file', 'fa-folder', 'fa-folder-open', 'fa-database', 'fa-server',
        'fa-laptop', 'fa-desktop', 'fa-mobile-alt', 'fa-tablet-alt', 'fa-print'
    ],
    'Media & Content': [
        'fa-camera', 'fa-image', 'fa-images', 'fa-video', 'fa-film',
        'fa-music', 'fa-microphone', 'fa-headphones', 'fa-play', 'fa-pause',
        'fa-stop', 'fa-volume-up', 'fa-volume-down', 'fa-volume-mute'
    ],
    'Shopping & E-commerce': [
        'fa-shopping-cart', 'fa-shopping-bag', 'fa-credit-card', 'fa-money-bill',
        'fa-gift', 'fa-box', 'fa-shipping-fast', 'fa-truck', 'fa-warehouse'
    ],
    'Health & Fitness': [
        'fa-dumbbell', 'fa-running', 'fa-walking', 'fa-swimming-pool', 'fa-bicycle',
        'fa-heartbeat', 'fa-medkit', 'fa-stethoscope', 'fa-pills', 'fa-hospital'
    ],
    'Fashion & Beauty': [
        'fa-tshirt', 'fa-hat-cowboy', 'fa-glasses', 'fa-shoe-prints', 'fa-ring',
        'fa-gem', 'fa-palette', 'fa-paint-brush', 'fa-makeup', 'fa-mirror'
    ],
    'Food & Dining': [
        'fa-utensils', 'fa-coffee', 'fa-pizza-slice', 'fa-hamburger', 'fa-birthday-cake',
        'fa-wine-glass', 'fa-beer', 'fa-ice-cream', 'fa-apple-alt', 'fa-carrot'
    ],
    'Transportation': [
        'fa-car', 'fa-plane', 'fa-ship', 'fa-motorcycle', 'fa-bus',
        'fa-train', 'fa-subway', 'fa-taxi', 'fa-ambulance', 'fa-fire-truck'
    ],
    'Weather & Nature': [
        'fa-sun', 'fa-moon', 'fa-cloud', 'fa-cloud-rain', 'fa-snowflake',
        'fa-leaf', 'fa-tree', 'fa-seedling', 'fa-mountain', 'fa-water'
    ],
    'Tools & Utilities': [
        'fa-wrench', 'fa-screwdriver', 'fa-hammer', 'fa-saw', 'fa-drill',
        'fa-key', 'fa-lock', 'fa-unlock', 'fa-shield-alt', 'fa-bug'
    ]
};

// Flatten all icons for search
const allIcons = Object.values(iconCategories).flat();

// Show enhanced icon picker modal
function showIconPicker(inputElement) {
    const currentIcon = inputElement.value || 'fa-star';
    
    // Create search bar
    const searchHTML = `
        <div class="mb-4">
            <div class="relative">
                <input type="text" id="icon-search" 
                       placeholder="ค้นหาไอคอน..." 
                       class="w-full px-4 py-3 pl-12 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 text-lg">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
            </div>
        </div>
    `;
    
    // Create category tabs
    const categoryTabs = Object.keys(iconCategories).map(category => 
        `<button type="button" class="category-tab px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-blue-100" data-category="${category}">${category}</button>`
    ).join('');
    
    // Create icons grid
    let iconsHTML = `
        <div class="mb-4">
            <div class="flex flex-wrap gap-2 mb-4">
                ${categoryTabs}
            </div>
        </div>
        <div id="icons-grid" class="grid grid-cols-8 gap-3 max-h-96 overflow-y-auto p-4 bg-gray-50 rounded-xl">
    `;
    
    // Add all icons
    allIcons.forEach(icon => {
        const isSelected = icon === currentIcon ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300';
        iconsHTML += `
            <button type="button" 
                    class="icon-option flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all duration-200 hover:shadow-md ${isSelected}"
                    data-icon="${icon}"
                    title="${icon}">
                <i class="fas ${icon} text-xl mb-1"></i>
                <span class="text-xs font-medium truncate w-full text-center">${icon.replace('fa-', '')}</span>
            </button>
        `;
    });
    
    iconsHTML += '</div>';
    
    Swal.fire({
        title: '<div class="flex items-center"><i class="fas fa-icons mr-3 text-blue-600"></i>เลือกไอคอน</div>',
        html: searchHTML + iconsHTML,
        width: '900px',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'rounded-2xl shadow-2xl',
            closeButton: 'text-gray-400 hover:text-gray-600 text-2xl'
        },
        didOpen: () => {
            const searchInput = document.getElementById('icon-search');
            const iconsGrid = document.getElementById('icons-grid');
            const categoryTabs = document.querySelectorAll('.category-tab');
            const iconOptions = document.querySelectorAll('.icon-option');
            
            // Search functionality
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                iconOptions.forEach(option => {
                    const iconName = option.dataset.icon.toLowerCase();
                    const displayName = option.querySelector('span').textContent.toLowerCase();
                    const isVisible = iconName.includes(searchTerm) || displayName.includes(searchTerm);
                    option.style.display = isVisible ? 'flex' : 'none';
                });
            });
            
            // Category filtering
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Update active tab
                    categoryTabs.forEach(t => t.classList.remove('bg-blue-500', 'text-white'));
                    tab.classList.add('bg-blue-500', 'text-white');
                    
                    // Filter icons
                    const category = tab.dataset.category;
                    const categoryIcons = iconCategories[category];
                    
                    iconOptions.forEach(option => {
                        const iconName = option.dataset.icon;
                        const isInCategory = categoryIcons.includes(iconName);
                        option.style.display = isInCategory ? 'flex' : 'none';
                    });
                });
            });
            
            // Set initial active tab
            categoryTabs[0].classList.add('bg-blue-500', 'text-white');
            
            // Icon selection
            iconOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const selectedIcon = this.dataset.icon;
                    inputElement.value = selectedIcon;
                    
                    // Update preview
                    const preview = document.getElementById(inputElement.id + '_preview');
                    if (preview) {
                        preview.innerHTML = `<i class="fas ${selectedIcon} text-3xl text-purple-600"></i>`;
                    }
                    
                    // Show success message
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `เลือกไอคอน: ${selectedIcon}`,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    
                    Swal.close();
                });
            });
        }
    });
}

// Initialize icon picker for all inputs
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.icon-picker-input').forEach(input => {
        const pickButton = input.nextElementSibling?.nextElementSibling;
        if (pickButton && pickButton.classList.contains('icon-picker-btn')) {
            pickButton.addEventListener('click', function() {
                showIconPicker(input);
            });
        }
    });
});

// Auto-update preview when typing
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('icon-picker-input')) {
        const preview = document.getElementById(e.target.id + '_preview');
        if (preview) {
            const iconName = e.target.value || 'fa-question';
            preview.innerHTML = `<i class="fas ${iconName} text-3xl text-purple-600"></i>`;
        }
    }
});