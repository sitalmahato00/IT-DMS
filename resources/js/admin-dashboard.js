import Chart from 'chart.js/auto';

// Professional color palette (muted, accessible)
const PALETTE = [
  '#3b82f6', // blue
  '#ef6432', // orange
  '#ec4899', // pink
  '#10b981', // green
  '#8b5cf6', // purple
  '#f59e0b', // amber
  '#06b6d4', // cyan
  '#6b7280'  // gray
];

function hexToRgba(hex, alpha = 0.85) {
  const c = hex.replace('#', '');
  const bigint = parseInt(c, 16);
  const r = (bigint >> 16) & 255;
  const g = (bigint >> 8) & 255;
  const b = bigint & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function buildColors(count, alpha = 0.85) {
  const colors = [];
  for (let i = 0; i < count; i++) {
    const hex = PALETTE[i % PALETTE.length];
    colors.push(hexToRgba(hex, alpha));
  }
  return colors;
}

function initChart(canvasId, configFactory) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;
  let parsed = {};
  try {
    parsed = JSON.parse(canvas.dataset.chart || '{}');
  } catch (e) {
    console.error('Failed to parse dataset for', canvasId, e);
    return;
  }

  const ctx = canvas.getContext('2d');
  // Ensure canvas fills parent height
  canvas.style.width = '100%';
  canvas.style.height = '100%';

  const config = configFactory(parsed, canvas);
  if (!config) return;
  // Make charts compact and polished
  config.options = config.options || {};
  config.options.responsive = true;
  config.options.maintainAspectRatio = false; // let container determine height
  config.options.plugins = config.options.plugins || {};
  config.options.plugins.legend = config.options.plugins.legend || { 
    position: 'bottom', 
    labels: { 
      boxWidth: 12, 
      padding: 16, 
      font: { size: 12 },
      usePointStyle: true,
      pointStyle: 'circle'
    } 
  };
  config.options.layout = config.options.layout || { padding: 16 };

  new Chart(ctx, config);
}

document.addEventListener('DOMContentLoaded', () => {
  // User Distribution - Horizontal Bar Chart with Animated Growth
  initChart('roleChart', (data) => {
    const labels = data.labels || [];
    const values = data.values || [];
    
    // Role-specific colors - vibrant and professional
    const roleColors = {
      'Student': { bg: 'rgba(59, 130, 246, 0.85)', border: '#3b82f6' },
      'Teacher': { bg: 'rgba(16, 185, 129, 0.85)', border: '#10b981' },
      'Parent': { bg: 'rgba(245, 158, 11, 0.85)', border: '#f59e0b' },
      'Admin': { bg: 'rgba(139, 92, 246, 0.85)', border: '#8b5cf6' }
    };
    
    const backgroundColor = labels.map(label => roleColors[label]?.bg || 'rgba(107, 114, 128, 0.85)');
    const borderColor = labels.map(label => roleColors[label]?.border || '#6b7280');
    const totalUsers = values.reduce((a, b) => a + b, 0);
    
    return {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Users',
          data: values,
          backgroundColor: backgroundColor,
          borderColor: borderColor,
          borderWidth: 2,
          borderRadius: 8,
          borderSkipped: false,
          barThickness: 32,
          hoverBackgroundColor: backgroundColor.map(c => c.replace('0.85', '1'))
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 1500,
          easing: 'easeOutQuart'
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            padding: 14,
            cornerRadius: 10,
            displayColors: false,
            callbacks: {
              title: function(context) {
                return context[0].label;
              },
              label: function(context) {
                const value = context.parsed.x;
                const percentage = totalUsers > 0 ? ((value / totalUsers) * 100).toFixed(1) : 0;
                return [
                  `Count: ${value}`,
                  `Share: ${percentage}%`
                ];
              },
              afterLabel: function(context) {
                return `Total Users: ${totalUsers}`;
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            grid: {
              color: 'rgba(15, 23, 42, 0.06)',
              drawBorder: false
            },
            ticks: {
              color: '#6b7280',
              font: { size: 12 },
              padding: 10,
              stepSize: Math.max(1, Math.ceil(totalUsers / 5))
            }
          },
          y: {
            grid: {
              display: false
            },
            ticks: {
              color: '#374151',
              font: { size: 13, weight: '500' },
              padding: 10
            }
          }
        }
      }
    };
  });

  // Attendance Overview - Single Green Line Chart (Attendance %)
  initChart('attChart', (data) => {
    const labels = data.labels || [];
    const percentageData = data.data || [];
    const details = data.details || [];
    
    return {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Attendance %',
          data: percentageData,
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          fill: true,
          tension: 0.4,
          borderWidth: 2,
          pointRadius: 4,
          pointBackgroundColor: '#22c55e',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        scales: {
          x: {
            ticks: { color: '#6B7280', font: { size: 11 } },
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            max: 100,
            ticks: { 
              color: '#6B7280', 
              precision: 0, 
              font: { size: 11 },
              callback: function(value) {
                return value + '%';
              }
            },
            grid: { color: 'rgba(15, 23, 42, 0.06)' }
          }
        },
        plugins: {
          tooltip: {
            mode: 'index',
            intersect: false,
            padding: 12,
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleFont: { size: 13 },
            bodyFont: { size: 12 },
            cornerRadius: 8,
            callbacks: {
              title: function(context) {
                return context[0].label + ' Attendance';
              },
              label: function(context) {
                const index = context.dataIndex;
                const detail = details[index];
                
                if (detail) {
                  return [
                    'Total Days: ' + detail.total,
                    'Attendance: ' + detail.percentage.toFixed(1) + '%'
                  ];
                }
                return 'Attendance: ' + context.parsed.y.toFixed(1) + '%';
              }
            }
          },
          legend: {
            position: 'top',
            align: 'end',
            labels: {
              boxWidth: 12,
              padding: 16,
              font: { size: 11 },
              usePointStyle: true,
              pointStyle: 'circle'
            }
          }
        }
      }
    };
  });
});

