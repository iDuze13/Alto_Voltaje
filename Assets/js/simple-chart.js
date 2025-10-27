// Interactive Chart Toggle System
console.log('Interactive chart toggle system loaded');

class DashboardChart {
    constructor() {
        this.canvas = null;
        this.chart = null;
        this.currentChart = 'profit';
        this.init();
    }

    init() {
        window.addEventListener('load', () => {
            this.setupElements();
            this.setupEventListeners();
            this.createChart('profit');
        });
    }

    setupElements() {
        this.canvas = document.getElementById('profitChart');
        this.loading = document.getElementById('chartLoading');
        this.chartTitle = document.getElementById('chartTitle');
        
        if (!this.canvas) {
            console.error('Canvas not found!');
            return;
        }
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not available');
            if (this.loading) this.loading.textContent = 'Chart.js not loaded';
            return;
        }
        
        console.log('Chart elements initialized');
    }

    setupEventListeners() {
        // Chart button toggles
        const chartButtons = document.querySelectorAll('.chart-btn');
        chartButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const chartType = e.target.dataset.chart;
                this.switchChart(chartType);
            });
        });

        // Metric card toggles
        const metricCards = document.querySelectorAll('.metric-card');
        metricCards.forEach(card => {
            card.addEventListener('click', (e) => {
                const chartType = e.currentTarget.dataset.chart;
                this.switchChart(chartType);
            });
        });
    }

    switchChart(chartType) {
        if (chartType === this.currentChart) return;
        
        console.log('Switching to chart:', chartType);
        
        // Update active states
        this.updateActiveStates(chartType);
        
        // Create new chart
        this.createChart(chartType);
        
        this.currentChart = chartType;
    }

    updateActiveStates(chartType) {
        // Update chart buttons
        document.querySelectorAll('.chart-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.chart === chartType);
        });

        // Update metric cards
        document.querySelectorAll('.metric-card').forEach(card => {
            card.classList.toggle('active', card.dataset.chart === chartType);
        });

        // Update chart title
        const titles = {
            profit: 'Total Profit Trend',
            orders: 'Total Orders Trend',
            impression: 'Impressions Trend'
        };
        
        if (this.chartTitle) {
            this.chartTitle.textContent = titles[chartType] || 'Chart';
        }
    }

    getChartData(chartType) {
        const labels = ['01 Jun', '02 Jun', '03 Jun', '04 Jun', '05 Jun', '06 Jun', '07 Jun', '08 Jun', '09 Jun', '10 Jun', '11 Jun', '12 Jun'];
        
        const datasets = {
            profit: {
                label: 'Profit',
                fillColor: 'rgba(16, 185, 129, 0.1)',
                strokeColor: '#10b981',
                pointColor: '#10b981',
                data: [120, 140, 135, 155, 180, 175, 195, 210, 235, 220, 245, 265]
            },
            orders: {
                label: 'Orders',
                fillColor: 'rgba(59, 130, 246, 0.1)',
                strokeColor: '#3b82f6',
                pointColor: '#3b82f6',
                data: [45, 52, 48, 61, 73, 68, 82, 89, 95, 91, 105, 118]
            },
            impression: {
                label: 'Impressions',
                fillColor: 'rgba(139, 92, 246, 0.1)',
                strokeColor: '#8b5cf6',
                pointColor: '#8b5cf6',
                data: [280, 320, 295, 350, 410, 385, 445, 480, 520, 495, 550, 590]
            }
        };

        return {
            labels: labels,
            datasets: [{
                ...datasets[chartType],
                pointStrokeColor: '#fff',
                pointHighlightFill: '#fff',
                pointHighlightStroke: datasets[chartType].strokeColor
            }]
        };
    }

    getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            scaleShowGridLines: true,
            scaleGridLineColor: '#f1f5f9',
            scaleGridLineWidth: 1,
            scaleShowHorizontalLines: true,
            scaleShowVerticalLines: false,
            bezierCurve: true,
            bezierCurveTension: 0.4,
            pointDot: true,
            pointDotRadius: 3,
            pointDotStrokeWidth: 2,
            pointHitDetectionRadius: 20,
            datasetStroke: true,
            datasetStrokeWidth: 2,
            datasetFill: true,
            animationSteps: 60,
            animationEasing: 'easeOutQuart'
        };
    }

    createChart(chartType) {
        try {
            // Show canvas and hide loading
            this.canvas.style.display = 'block';
            if (this.loading) this.loading.style.display = 'none';
            
            // Destroy existing chart
            if (this.chart && this.chart.destroy) {
                this.chart.destroy();
            }
            
            // Get canvas context
            const ctx = this.canvas.getContext('2d');
            
            // Clear canvas
            ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            
            // Get chart data and options
            const chartData = this.getChartData(chartType);
            const chartOptions = this.getChartOptions();
            
            // Create the line chart using Chart.js 1.x
            this.chart = new Chart(ctx).Line(chartData, chartOptions);
            
            console.log(`${chartType} chart created successfully!`);
            
        } catch (error) {
            console.error(`${chartType} chart failed:`, error);
            if (this.loading) {
                this.loading.textContent = 'Chart error: ' + error.message;
                this.loading.style.color = 'red';
            }
        }
    }
}

// Initialize the dashboard chart system
new DashboardChart();

// Channel Revenue Wheel Chart
class ChannelRevenueChart {
    constructor() {
        this.canvas = null;
        this.chart = null;
        this.init();
    }

    init() {
        window.addEventListener('load', () => {
            this.setupChart();
        });
    }

    setupChart() {
        this.canvas = document.getElementById('channelRevenueChart');
        
        if (!this.canvas || typeof Chart === 'undefined') {
            console.error('Channel chart canvas or Chart.js not found');
            return;
        }

        console.log('Creating channel revenue wheel chart...');
        
        try {
            const ctx = this.canvas.getContext('2d');
            
            // Chart.js 1.x Doughnut chart data
            const chartData = [
                {
                    value: 2900,
                    color: '#3b82f6',
                    highlight: '#60a5fa',
                    label: 'Online store'
                },
                {
                    value: 2600,
                    color: '#10b981',
                    highlight: '#34d399',
                    label: 'Physical store'
                },
                {
                    value: 2100,
                    color: '#f59e0b',
                    highlight: '#fbbf24',
                    label: 'Social Media'
                }
            ];

            const chartOptions = {
                responsive: true,
                maintainAspectRatio: true,
                segmentShowStroke: true,
                segmentStrokeColor: '#fff',
                segmentStrokeWidth: 2,
                percentageInnerCutout: 60,
                animationSteps: 60,
                animationEasing: 'easeOutBounce',
                animateRotate: true,
                animateScale: false,
                legendTemplate: ''
            };

            // Create the doughnut chart
            this.chart = new Chart(ctx).Doughnut(chartData, chartOptions);
            
            console.log('Channel revenue wheel chart created successfully!');
            
        } catch (error) {
            console.error('Channel chart creation failed:', error);
        }
    }
}

// Initialize the channel revenue chart
new ChannelRevenueChart();

// Top Products Interaction
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for product items
    const productItems = document.querySelectorAll('.product-item[data-product-id]');
    
    productItems.forEach(item => {
        item.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.querySelector('.product-name').textContent;
            
            // You can redirect to product detail page or show modal
            console.log('Product clicked:', productId, productName);
            
            // Example: redirect to product edit page
            // window.location.href = `/AltoVoltaje/productos/editar/${productId}`;
            
            // For now, just highlight the clicked item
            productItems.forEach(p => p.classList.remove('selected'));
            this.classList.add('selected');
        });
        
        // Add hover effect for better UX
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});