/**
 * Dashboard JavaScript Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
    
    // Update time every minute
    updateTime();
    setInterval(updateTime, 60000);
    
    // Auto-refresh data every 5 minutes
    setInterval(refreshDashboardData, 300000);
});

function initializeDashboard() {
    // Add click handlers for metric cards
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach(card => {
        card.addEventListener('click', function() {
            const chartType = this.dataset.chart;
            if (chartType) {
                switchChart(chartType);
                updateActiveCard(this);
            }
        });
    });

    // Add hover effects for stat items
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add click handlers for activity items
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach(item => {
        item.addEventListener('click', function() {
            // Add subtle animation
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // Initialize tooltips
    initializeTooltips();
    
    // Load initial chart
    loadChart('profit');
}

function switchChart(chartType) {
    const chartTitle = document.getElementById('chartTitle');
    const chartButtons = document.querySelectorAll('.chart-btn');
    
    // Update chart title
    const titles = {
        'profit': 'Tendencias de Ganancias',
        'orders': 'Tendencias de Pedidos',
        'impression': 'Tendencias de Interacciones'
    };
    
    if (chartTitle) {
        chartTitle.textContent = titles[chartType] || 'Tendencias';
    }
    
    // Update active button
    chartButtons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.chart === chartType) {
            btn.classList.add('active');
        }
    });
    
    // Load chart data
    loadChart(chartType);
}

function updateActiveCard(activeCard) {
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach(card => {
        card.classList.remove('active');
    });
    activeCard.classList.add('active');
}

function loadChart(chartType) {
    const chartLoading = document.getElementById('chartLoading');
    const chartCanvas = document.getElementById('profitChart');
    
    if (chartLoading) {
        chartLoading.style.display = 'block';
        chartLoading.textContent = 'Cargando datos...';
    }
    
    if (chartCanvas) {
        chartCanvas.style.display = 'none';
    }
    
    // Simulate chart loading
    setTimeout(() => {
        if (chartLoading) {
            chartLoading.style.display = 'none';
        }
        if (chartCanvas) {
            chartCanvas.style.display = 'block';
        }
        
        // Here you would load actual chart data
        console.log(`Loading chart: ${chartType}`);
    }, 1000);
}

function refreshDashboardData() {
    console.log('Refreshing dashboard data...');
    
    // Add refresh indicator
    const refreshIndicator = document.createElement('div');
    refreshIndicator.className = 'refresh-indicator';
    refreshIndicator.innerHTML = '<i class="fa-solid fa-sync fa-spin"></i> Actualizando...';
    refreshIndicator.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #3b82f6;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    document.body.appendChild(refreshIndicator);
    
    // Animate in
    setTimeout(() => {
        refreshIndicator.style.opacity = '1';
    }, 100);
    
    // Simulate data refresh
    setTimeout(() => {
        refreshIndicator.style.opacity = '0';
        setTimeout(() => {
            if (refreshIndicator.parentNode) {
                refreshIndicator.parentNode.removeChild(refreshIndicator);
            }
        }, 300);
    }, 2000);
}

function initializeTooltips() {
    // Add tooltips for stat items
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach(item => {
        const label = item.querySelector('.stat-label');
        const value = item.querySelector('.stat-value');
        
        if (label && value) {
            item.setAttribute('title', `${label.textContent}: ${value.textContent}`);
        }
    });
    
    // Add tooltips for product items
    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach(item => {
        const productName = item.querySelector('.product-name');
        if (productName) {
            item.setAttribute('title', productName.getAttribute('title') || productName.textContent);
        }
    });
}

// Utility functions
function formatNumber(num) {
    if (num >= 1000000000) {
        return (num / 1000000000).toFixed(1) + 'B';
    }
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function animateNumber(element, targetValue, duration = 1000) {
    const startValue = 0;
    const startTime = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const currentValue = startValue + (targetValue - startValue) * progress;
        element.textContent = formatNumber(Math.round(currentValue));
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

function updateTime() {
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        timeElement.textContent = `${hours}:${minutes}`;
    }
}

// Export functions for external use
window.dashboardUtils = {
    refreshData: refreshDashboardData,
    switchChart: switchChart,
    formatNumber: formatNumber,
    animateNumber: animateNumber,
    updateTime: updateTime
};