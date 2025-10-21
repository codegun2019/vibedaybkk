<?php
/**
 * Navigation Styles
 * CSS สำหรับ Navigation
 */
?>
<style>
    /* Navigation Styles */
    .nav-link {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .nav-link.active {
        color: #DC2626 !important;
        font-weight: 600;
    }
    
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #DC2626;
        transition: width 0.3s ease;
    }
    
    .nav-link:hover::after {
        width: 100%;
    }
    
    /* Mobile Menu Styles */
    .mobile-menu {
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
        background: linear-gradient(135deg, rgba(10,10,10,0.98) 0%, rgba(26,26,26,0.98) 50%, rgba(10,10,10,0.98) 100%) !important;
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        box-shadow: -10px 0 50px rgba(0, 0, 0, 0.5);
    }
    
    .mobile-menu.open {
        transform: translateX(0);
    }
    
    /* Mobile Menu Links */
    .mobile-menu-link {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
        backdrop-filter: blur(10px);
    }
    
    .mobile-menu-link:hover {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.15) 0%, rgba(220, 38, 38, 0.08) 100%) !important;
        transform: translateX(-3px);
    }
    
    .mobile-menu-link:active {
        transform: translateX(-3px) scale(0.98);
    }
    
    /* Mobile Menu Overlay */
    @media (max-width: 768px) {
        .mobile-menu::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .mobile-menu.open::before {
            opacity: 1;
            pointer-events: auto;
        }
    }
    
    /* Smooth Scrollbar for Mobile Menu */
    .mobile-menu::-webkit-scrollbar {
        width: 6px;
    }
    
    .mobile-menu::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    
    .mobile-menu::-webkit-scrollbar-thumb {
        background: rgba(220, 38, 38, 0.5);
        border-radius: 10px;
    }
    
    .mobile-menu::-webkit-scrollbar-thumb:hover {
        background: rgba(220, 38, 38, 0.7);
    }
</style>

