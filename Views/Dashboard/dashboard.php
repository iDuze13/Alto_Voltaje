<?php 
require_once(__DIR__ . '/../../Helpers/DashboardHelpers.php');
headerAdmin($data); 
?>
<main class="app-content admin-dash">
  <!-- Welcome Section -->
  <div class="welcome-section">
    <div class="welcome-content">
      <h1>¡Bienvenido al Panel de Alto Voltaje!</h1>
      <p>Resumen completo de tu negocio en tiempo real</p>
    </div>
    <div class="welcome-stats">
      <div class="quick-stat">
        <span class="quick-stat-label">Hoy</span>
        <span class="quick-stat-value"><?= date('d/m/Y'); ?></span>
      </div>
      <div class="quick-stat">
        <span class="quick-stat-label">Hora</span>
        <span class="quick-stat-value" id="currentTime"><?= date('H:i'); ?></span>
      </div>
    </div>
  </div>
  
  <div class="dashboard-container">
    <!-- Main Content Area -->
    <div class="main-content">
      <!-- Overview Section -->
      <div class="overview-section">
        <div class="section-header">
          <h2>Resumen</h2>
          <select class="period-selector">
            <option>Mensual</option>
            <option>Semanal</option>
            <option>Diario</option>
          </select>
        </div>
        
        <!-- Key Metrics Row -->
        <div class="key-metrics">
          <div class="metric-card profit active" data-chart="profit">
            <div class="metric-icon"><i class="fa-solid fa-warehouse"></i></div>
            <div class="metric-info">
              <span class="metric-label">Valor del Inventario</span>
              <span class="metric-value"><?= formatCurrency($data['metrics']['total_inventory_value'] ?? 0); ?></span>
              <span class="metric-change positive">Total en stock</span>
            </div>
            <div class="metric-toggle"></div>
          </div>
          <div class="metric-card orders" data-chart="orders">
            <div class="metric-icon"><i class="fa-solid fa-box"></i></div>
            <div class="metric-info">
              <span class="metric-label">Productos Activos</span>
              <span class="metric-value"><?= formatLargeNumber($data['metrics']['total_products'] ?? 0); ?></span>
              <span class="metric-change <?= ($data['metrics']['low_stock_products'] ?? 0) > 0 ? 'negative' : 'positive'; ?>">
                <?php if (($data['metrics']['low_stock_products'] ?? 0) > 0): ?>
                  <?= $data['metrics']['low_stock_products']; ?> con stock bajo
                <?php else: ?>
                  Stock saludable
                <?php endif; ?>
              </span>
            </div>
            <div class="metric-toggle"></div>
          </div>
          <div class="metric-card impression" data-chart="impression">
            <div class="metric-icon"><i class="fa-solid fa-users"></i></div>
            <div class="metric-info">
              <span class="metric-label">Usuarios Totales</span>
              <span class="metric-value"><?= formatLargeNumber($data['metrics']['users_stats']['total_users'] ?? 0); ?></span>
              <span class="metric-change positive">
                <?= $data['metrics']['users_stats']['total_clients'] ?? 0; ?> clientes, 
                <?= $data['metrics']['users_stats']['total_employees'] ?? 0; ?> empleados
              </span>
            </div>
            <div class="metric-toggle"></div>
          </div>
        </div>

        <!-- Chart Area -->
        <div class="chart-section">
          <div class="chart-header">
            <h4 id="chartTitle">Tendencias de Ganancias</h4>
            <div class="chart-controls">
              <button class="chart-btn active" data-chart="profit">Ganancias</button>
              <button class="chart-btn" data-chart="orders">Pedidos</button>
              <button class="chart-btn" data-chart="impression">Interacciones</button>
            </div>
          </div>
          <div class="chart-container">
            <div class="chart-loading" id="chartLoading">Cargando diagrama...</div>
            <canvas id="profitChart" style="display: none;"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent Orders Section -->
      <div class="orders-section">
        <div class="section-header">
          <h3>Últimas órdenes</h3>
          <button class="view-all-btn">Ver Órdenes</button>
        </div>
        <div class="orders-table">
          <table>
            <thead>
              <tr>
                <th>ORDEN</th>
                <th>ESTADO</th>
                <th>FECHA</th>
                <th>CLIENTE</th>
                <th>MONTO GASTADO</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($data['recent_orders'])): ?>
                <tr>
                  <td colspan="5" style="color: #64748b; text-align: center; padding: 20px;">No se encontraron órdenes recientes</td>
                </tr>
              <?php else: ?>
                <?php foreach ($data['recent_orders'] as $order): ?>
                  <tr>
                    <td><?= htmlspecialchars($order['idPedido']); ?></td>
                    <td>
                      <span class="status <?= getStatusColor($order['Estado_Pedido']); ?>">
                        <?= getStatusText($order['Estado_Pedido']); ?>
                      </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($order['Fecha_Pedido'])); ?></td>
                    <td><?= htmlspecialchars($order['Nombre_Usuario'] . ' ' . $order['Apellido_Usuario']); ?></td>
                    <td><?= formatCurrency($order['Total_Pedido']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
          </tbody>
        </table>
        </div>
      </div>

      <!-- Recent Reviews Section -->
      <div class="reviews-section">
        <div class="section-header">
          <h3>Últimas reseñas</h3>
          <button class="view-all-btn">⋯</button>
        </div>
        <div class="reviews-list">
          <?php if (empty($data['recent_reviews'])): ?>
            <div class="no-reviews">
              <p style="color: #64748b; text-align: center; padding: 20px;">No se encontraron reseñas</p>
            </div>
          <?php else: ?>
            <?php foreach ($data['recent_reviews'] as $review): ?>
              <div class="review-item">
                <div class="review-product">
                  <div class="product-image">
                    <img src="<?= media(); ?>/images/<?= $review['product_image']; ?>" 
                         alt="<?= htmlspecialchars($review['product_name']); ?>">
                  </div>
                  <div class="product-info">
                    <span class="product-name" title="<?= htmlspecialchars($review['product_name']); ?>">
                      <?= htmlspecialchars($review['product_name']); ?>
                    </span>
                    <span class="reviewer-name">Reseñado por <?= htmlspecialchars($review['reviewer_name']); ?></span>
                  </div>
                </div>
                <div class="review-rating">
                  <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <span class="star <?= $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sales Target -->
      <div class="sidebar-card">
        <div class="card-header">
          <h3>Objetivo de ventas</h3>
          <select class="period-selector">
            <option>Anual</option>
            <option>Mensual</option>
            <option>Semanal</option>
          </select>
        </div>
        <div class="sales-target">
          <div class="target-info">
            <span class="target-value">15.8 Mil</span>
            <span class="target-subtitle">/ 22.0 Mil Unidades</span>
            <span class="target-period">Realizado este año</span>
          </div>
          <div class="progress-ring">
            <svg class="progress-ring-svg" width="80" height="80">
              <circle cx="40" cy="40" r="32" stroke="#e5e7eb" stroke-width="8" fill="none" />
              <circle cx="40" cy="40" r="32" stroke="#3b82f6" stroke-width="8" fill="none" 
                      stroke-dasharray="201" stroke-dashoffset="50" stroke-linecap="round" />
            </svg>
            <span class="progress-percentage">71%</span>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="sidebar-card">
        <div class="card-header">
          <h3>Productos más Vendidos</h3>
          <a href="<?= base_url(); ?>/productos/listar" class="view-all-btn">Ver todos</a>
        </div>
        <div class="top-products">
          <?php if (empty($data['top_products'])): ?>
            <div class="no-products">
              <p style="color: #64748b; text-align: center; padding: 20px;">No se encontraron productos</p>
            </div>
          <?php else: ?>
            <?php foreach ($data['top_products'] as $product): ?>
              <div class="product-item" data-product-id="<?= $product['idProducto']; ?>">
                <div class="product-image">
                  <img src="<?= $product['Product_Image']; ?>" 
                       alt="<?= htmlspecialchars($product['Nombre_Producto']); ?>"
                       onerror="this.src='<?= media(); ?>/images/default-product.svg'">
                </div>
                <div class="product-info">
                  <span class="product-name" title="<?= htmlspecialchars($product['Nombre_Producto']); ?>">
                    <?= htmlspecialchars(strlen($product['Nombre_Producto']) > 20 ? substr($product['Nombre_Producto'], 0, 20) . '...' : $product['Nombre_Producto']); ?>
                  </span>
                  <span class="product-sales">Vendidos: <?= number_format($product['Sales_Count']); ?></span>
                  <span class="product-price">$<?= number_format($product['Precio_Venta'], 2); ?></span>
                </div>
                <div class="product-stats">
                  <span class="product-change <?= $product['Growth_Percentage'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?= $product['Growth_Percentage'] >= 0 ? '+' : ''; ?><?= number_format($product['Growth_Percentage'], 1); ?>%
                  </span>
                  <?php if ($product['Es_Destacado']): ?>
                    <span class="product-badge featured">⭐</span>
                  <?php endif; ?>
                  <?php if ($product['En_Oferta']): ?>
                    <span class="product-badge sale">🏷️</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Database Statistics
      <div class="sidebar-card">
        <div class="card-header">
          <h3>Estadísticas del Sistema</h3>
        </div>
        <div class="system-stats">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="stat-info">
              <span class="stat-value"><?= formatLargeNumber($data['metrics']['total_products'] ?? 0); ?></span>
              <span class="stat-label">Productos Activos</span>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-exclamation-triangle"></i></div>
            <div class="stat-info">
              <span class="stat-value <?= ($data['metrics']['low_stock_products'] ?? 0) > 0 ? 'warning' : ''; ?>">
                <?= formatLargeNumber($data['metrics']['low_stock_products'] ?? 0); ?>
              </span>
              <span class="stat-label">Stock Bajo</span>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
            <div class="stat-info">
              <span class="stat-value"><?= formatLargeNumber(count($data['metrics']['products_by_category'] ?? [])); ?></span>
              <span class="stat-label">Categorías</span>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-truck"></i></div>
            <div class="stat-info">
              <span class="stat-value"><?= formatLargeNumber(count($data['providers'] ?? [])); ?></span>
              <span class="stat-label">Proveedores</span>
            </div>
          </div>
        </div>
        
      <!-- Recent Activity -->
      <div class="sidebar-card">
        <div class="card-header">
          <h3>Actividad Reciente</h3>
        </div>
        <div class="activity-list">
          <?php if (!empty($data['metrics']['recent_activity'])): ?>
            <?php foreach ($data['metrics']['recent_activity'] as $activity): ?>
              <div class="activity-item">
                <div class="activity-icon">
                  <i class="fa-solid <?= $activity['icon']; ?>"></i>
                </div>
                <div class="activity-info">
                  <span class="activity-message"><?= htmlspecialchars($activity['message']); ?></span>
                  <span class="activity-date"><?= date('d/m/Y H:i', strtotime($activity['date'])); ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-activity">
              <p style="color: #64748b; text-align: center; padding: 20px;">No hay actividad reciente</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Channel Revenue -->
      <div class="sidebar-card">
        <div class="card-header">
          <h3>Ingresos por canal</h3>
          <select class="period-selector">
            <option>Mensual</option>
            <option>Semanal</option>
            <option>Diario</option>
          </select>
        </div>
        <div class="channel-revenue">
          <div class="revenue-stat">
            <span class="revenue-percentage">3.4%</span>
            <span class="revenue-label">Tasa de crecimiento</span>
          </div>
          
          <div class="wheel-chart-container">
            <canvas id="channelRevenueChart" width="160" height="160"></canvas>
            <div class="chart-center">
              <span class="total-revenue">$7.6 Mil</span>
              <span class="total-label">Total</span>
            </div>
          </div>
          
          <div class="channel-legend">
            <div class="legend-item">
              <div class="legend-color blue"></div>
              <div class="legend-info">
                <span class="legend-value">$2.9 Mil</span>
                <span class="legend-label">Tienda online</span>
              </div>
              <span class="legend-percentage">38%</span>
            </div>
            
            <div class="legend-item">
              <div class="legend-color green"></div>
              <div class="legend-info">
                <span class="legend-value">$2.6 Mil</span>
                <span class="legend-label">Tienda física</span>
              </div>
              <span class="legend-percentage">34%</span>
            </div>
            
            <div class="legend-item">
              <div class="legend-color orange"></div>
              <div class="legend-info">
                <span class="legend-value">$2.1 Mil</span>
                <span class="legend-label">Redes sociales</span>
              </div>
              <span class="legend-percentage">28%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>