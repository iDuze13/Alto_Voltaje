// Function to initialize chart
function initializeChart() {
  console.log('Initializing chart...');
  
  // Get loading indicator and canvas
  const loadingEl = document.getElementById('chartLoading');
  const profitCanvas = document.getElementById('profitChart');
  
  console.log('Loading element found:', !!loadingEl);
  console.log('Canvas found:', !!profitCanvas);
  console.log('Canvas element:', profitCanvas);
  console.log('Chart.js available:', !!window.Chart);
  console.log('Chart constructor:', typeof window.Chart);
  
  if (!profitCanvas) {
    console.error('Canvas element not found!');
    if (loadingEl) {
      loadingEl.textContent = 'Chart canvas not found';
      loadingEl.style.color = '#dc3545';
    }
    return;
  }
  
  if (!window.Chart) {
    console.error('Chart.js not loaded!');
    if (loadingEl) {
      loadingEl.textContent = 'Chart.js library not loaded';
      loadingEl.style.color = '#dc3545';
    }
    return;
  }
  
  try {
    // Show loading message
    if (loadingEl) {
      loadingEl.textContent = 'Initializing chart...';
    }
    
    // Test canvas context
    const ctx = profitCanvas.getContext('2d');
    console.log('Canvas context:', ctx);
    console.log('Canvas width:', profitCanvas.width, 'height:', profitCanvas.height);
    console.log('Canvas parent:', profitCanvas.parentElement);
    
    // Set canvas size explicitly
    const chartContainer = profitCanvas.parentElement;
    if (chartContainer) {
      profitCanvas.width = chartContainer.offsetWidth || 800;
      profitCanvas.height = chartContainer.offsetHeight || 280;
      console.log('Set canvas size to:', profitCanvas.width, 'x', profitCanvas.height);
    }
    
    const labels = ['01 Jun', '02 Jun', '03 Jun', '04 Jun', '05 Jun', '06 Jun', '07 Jun', '08 Jun', '09 Jun', '10 Jun', '11 Jun', '12 Jun'];
    const data = [120, 140, 135, 155, 180, 175, 195, 210, 235, 220, 245, 265];
    
    console.log('Creating Chart.js instance...');
    console.log('Data:', data);
    console.log('Labels:', labels);
    
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Profit',
          data,
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 0,
          pointHoverRadius: 6,
          borderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { 
            mode: 'index', 
            intersect: false,
            callbacks: {
              label: function(context) {
                return 'Profit: $' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        interaction: { mode: 'index', intersect: false },
        scales: {
          x: {
            grid: { display: false },
            ticks: { 
              color: '#64748b',
              font: { size: 11 }
            },
            border: { display: false }
          },
          y: {
            min: 100,
            max: 300,
            grid: { 
              color: '#f1f5f9',
              drawBorder: false
            },
            ticks: { 
              color: '#64748b',
              font: { size: 11 },
              stepSize: 50,
              callback: function(value) {
                return value;
              }
            },
            border: { display: false }
          }
        }
      }
    });
    
    console.log('Chart created successfully:', chart);
    
    // Test if chart is actually rendered
    setTimeout(() => {
      const imageData = ctx.getImageData(0, 0, profitCanvas.width, profitCanvas.height);
      const hasData = imageData.data.some(channel => channel !== 0);
      console.log('Chart has rendered data:', hasData);
      
      if (!hasData) {
        console.warn('Chart appears empty, drawing test line...');
        // Draw a simple test line
        ctx.strokeStyle = '#10b981';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(50, 200);
        ctx.lineTo(150, 100);
        ctx.lineTo(250, 150);
        ctx.lineTo(350, 80);
        ctx.stroke();
      }
    }, 500);
    
    // Hide loading and show canvas
    if (loadingEl) {
      loadingEl.style.display = 'none';
    }
    profitCanvas.style.display = 'block';
    
    // Add success indicator
    console.log('✓ Chart successfully created and displayed');
    
    // Set container height if not set
    const container = profitCanvas.parentElement;
    if (container && !container.style.height) { 
      container.style.height = '280px'; 
    }
    
    // Force resize after creation
    setTimeout(() => {
      chart.resize();
      console.log('Chart resized after creation');
    }, 100);
    
    // Resize observer
    if (window.ResizeObserver) {
      const ro = new ResizeObserver(() => {
        try {
          chart.resize();
          console.log('Chart resized via ResizeObserver');
        } catch (error) {
          console.error('Error during resize:', error);
        }
      });
      ro.observe(container);
    } else {
      window.addEventListener('resize', () => {
        try {
          chart.resize();
        } catch (error) {
          console.error('Error during window resize:', error);
        }
      });
    }
    
  } catch (error) {
    console.error('Chart creation failed:', error);
    if (loadingEl) {
      loadingEl.textContent = 'Error loading chart: ' + error.message;
      loadingEl.style.color = '#dc3545';
    }
  }
  
  // Mark as initialized
  window.chartInitialized = true;
}

// Try to initialize immediately if DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeChart);
} else {
  // DOM is already ready
  initializeChart();
}

// Also try with a slight delay as fallback
setTimeout(() => {
  if (!window.chartInitialized) {
    console.log('Fallback chart initialization...');
    initializeChart();
  }
}, 1000);
