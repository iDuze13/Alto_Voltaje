/**
 * Responsive Dashboard JavaScript
 * Handles dynamic responsive behavior and mobile interactions
 */

(function() {
    'use strict';
    
    let resizeTimer;
    let currentBreakpoint = '';
    
    // Breakpoint definitions
    const breakpoints = {
        mobile: 479,
        tablet: 767,
        tabletLandscape: 1023,
        desktop: 1199,
        largeDesktop: 1439
    };
    
    /**
     * Get current breakpoint
     */
    function getCurrentBreakpoint() {
        const width = window.innerWidth;
        
        if (width <= breakpoints.mobile) return 'mobile';
        if (width <= breakpoints.tablet) return 'tablet';
        if (width <= breakpoints.tabletLandscape) return 'tablet-landscape';
        if (width <= breakpoints.desktop) return 'desktop';
        if (width <= breakpoints.largeDesktop) return 'large-desktop';
        return 'xl-desktop';
    }
    
    /**
     * Handle responsive table
     */
    function handleResponsiveTable() {
        const tables = document.querySelectorAll('.admin-dash .orders-table table');
        const breakpoint = getCurrentBreakpoint();
        
        tables.forEach(table => {
            if (breakpoint === 'mobile') {
                // Add mobile-specific table handling
                table.classList.add('mobile-table');
                
                // Convert table to card layout for mobile
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    row.classList.add('mobile-row');
                });
            } else {
                table.classList.remove('mobile-table');
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    row.classList.remove('mobile-row');
                });
            }
        });
    }
    
    /**
     * Handle responsive sidebar
     */
    function handleResponsiveSidebar() {
        const sidebar = document.querySelector('.admin-dash .sidebar');
        const breakpoint = getCurrentBreakpoint();
        
        if (!sidebar) return;
        
        // Add responsive classes
        sidebar.classList.remove('sidebar-mobile', 'sidebar-tablet', 'sidebar-desktop');
        
        if (breakpoint === 'mobile') {
            sidebar.classList.add('sidebar-mobile');
        } else if (breakpoint === 'tablet' || breakpoint === 'tablet-landscape') {
            sidebar.classList.add('sidebar-tablet');
        } else {
            sidebar.classList.add('sidebar-desktop');
        }
    }
    
    /**
     * Handle responsive charts
     */
    function handleResponsiveCharts() {
        const chartContainers = document.querySelectorAll('.admin-dash .chart-container');
        const breakpoint = getCurrentBreakpoint();
        
        chartContainers.forEach(container => {
            const canvas = container.querySelector('canvas');
            if (!canvas) return;
            
            // Adjust chart size based on breakpoint
            let height = 300;
            if (breakpoint === 'mobile') height = 200;
            else if (breakpoint === 'tablet') height = 240;
            
            container.style.height = height + 'px';
            
            // Trigger chart resize if Chart.js is available
            if (window.Chart && canvas.chart) {
                canvas.chart.resize();
            }
        });
    }
    
    /**
     * Handle touch interactions
     */
    function setupTouchInteractions() {
        // Add touch feedback to interactive elements
        const interactiveElements = document.querySelectorAll(
            '.admin-dash .metric-card, .admin-dash .product-item, .admin-dash .activity-item, .admin-dash .view-all-btn'
        );
        
        interactiveElements.forEach(element => {
            element.addEventListener('touchstart', function() {
                this.classList.add('touch-active');
            });
            
            element.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.classList.remove('touch-active');
                }, 150);
            });
            
            element.addEventListener('touchcancel', function() {
                this.classList.remove('touch-active');
            });
        });
    }
    
    /**
     * Handle responsive font sizes
     */
    function handleResponsiveFonts() {
        const breakpoint = getCurrentBreakpoint();
        const dashboard = document.querySelector('.admin-dash');
        
        if (!dashboard) return;
        
        // Remove existing font size classes
        dashboard.classList.remove('font-mobile', 'font-tablet', 'font-desktop');
        
        // Add appropriate font size class
        if (breakpoint === 'mobile') {
            dashboard.classList.add('font-mobile');
        } else if (breakpoint === 'tablet' || breakpoint === 'tablet-landscape') {
            dashboard.classList.add('font-tablet');
        } else {
            dashboard.classList.add('font-desktop');
        }
    }
    
    /**
     * Handle viewport height for mobile
     */
    function handleViewportHeight() {
        if (window.innerWidth <= breakpoints.mobile) {
            // Set CSS custom property for real viewport height
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
    }
    
    /**
     * Initialize responsive features
     */
    function initResponsive() {
        currentBreakpoint = getCurrentBreakpoint();
        
        handleResponsiveTable();
        handleResponsiveSidebar();
        handleResponsiveCharts();
        handleResponsiveFonts();
        handleViewportHeight();
        setupTouchInteractions();
        
        console.log('Dashboard responsive initialized for:', currentBreakpoint);
    }
    
    /**
     * Handle window resize
     */
    function handleResize() {
        clearTimeout(resizeTimer);
        
        resizeTimer = setTimeout(() => {
            const newBreakpoint = getCurrentBreakpoint();
            
            // Only reinitialize if breakpoint changed
            if (newBreakpoint !== currentBreakpoint) {
                currentBreakpoint = newBreakpoint;
                initResponsive();
            }
            
            handleViewportHeight();
            handleResponsiveCharts();
        }, 150);
    }
    
    /**
     * Handle orientation change (mobile)
     */
    function handleOrientationChange() {
        setTimeout(() => {
            handleViewportHeight();
            handleResponsiveCharts();
        }, 100);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResponsive);
    } else {
        initResponsive();
    }
    
    // Event listeners
    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleOrientationChange);
    
    // Export for external use
    window.responsiveDashboard = {
        init: initResponsive,
        getCurrentBreakpoint: getCurrentBreakpoint,
        handleResize: handleResize
    };
    
})();