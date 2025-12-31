<?php
// backend/dashboard.php
require_once 'session.php';
require_once 'config.php';

// التحقق من تسجيل الدخول
requireAdminLogin();

// الحصول على إحصائيات
$stats = [];

// عدد الطلبات
$sql_orders = "SELECT 
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    AVG(total) as avg_order,
    COUNT(CASE WHEN statut = 'en attente' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN statut = 'confirmee' THEN 1 END) as confirmed_orders,
    COUNT(CASE WHEN statut = 'livree' THEN 1 END) as delivered_orders
    FROM commandes";
$result = $conn->query($sql_orders);
$stats = $result->fetch_assoc();

// عدد المنتجات
$sql_products = "SELECT COUNT(*) as total_products FROM produits";
$result = $conn->query($sql_products);
$stats['total_products'] = $result->fetch_assoc()['total_products'];

// الطلبات الأخيرة
$sql_recent = "SELECT * FROM commandes ORDER BY date_commande DESC LIMIT 5";
$recent_orders = $conn->query($sql_recent);

// أفضل المنتجات مبيعاً (لو كان لديك نظام تتبع)
// $sql_top = "SELECT produits FROM commandes";
// ستحتاج لتطوير هذا الجزء لاحقاً
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #7b3fe4;
            --secondary: #c95bee;
            --dark: #1a1a2e;
            --darker: #16213e;
            --light: #f8f9fa;
            --danger: #e74c3c;
            --success: #2ecc71;
            --warning: #f39c12;
            --info: #3498db;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f7fb;
            color: #333;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: var(--dark);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .logo {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }
        
        .logo h2 {
            color: var(--secondary);
            font-size: 24px;
        }
        
        .logo p {
            color: #aaa;
            font-size: 12px;
        }
        
        .nav-links {
            list-style: none;
            padding: 0 20px;
        }
        
        .nav-links li {
            margin-bottom: 10px;
        }
        
        .nav-links a {
            color: #ddd;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }
        
        .nav-links a:hover,
        .nav-links a.active {
            background: var(--primary);
            color: white;
        }
        
        .nav-links i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Topbar */
        .topbar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .btn-logout {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn-logout:hover {
            background: #c0392b;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            border-left: 5px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.orders {
            border-left-color: var(--primary);
        }
        
        .stat-card.revenue {
            border-left-color: var(--success);
        }
        
        .stat-card.pending {
            border-left-color: var(--warning);
        }
        
        .stat-card.products {
            border-left-color: var(--info);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-card.orders .stat-icon {
            background: rgba(123, 63, 228, 0.1);
            color: var(--primary);
        }
        
        .stat-card.revenue .stat-icon {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }
        
        .stat-card.pending .stat-icon {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }
        
        .stat-card.products .stat-icon {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        /* Recent Orders */
        .recent-orders {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .section-title h3 {
            color: var(--dark);
        }
        
        .btn-view-all {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table thead {
            background: #f8f9fa;
        }
        
        table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
        }
        
        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-delivered {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            background: #f8f9fa;
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }
        
        .action-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .action-btn i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .logo h2,
            .sidebar .nav-links span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .nav-links a {
                justify-content: center;
                padding: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .topbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h2><i class="fas fa-laptop"></i> Admin</h2>
            <p>PC PortableTech</p>
        </div>
        
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> <span>Tableau de Bord</span></a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Commandes</span></a></li>
            <li><a href="products.php"><i class="fas fa-laptop"></i> <span>Produits</span></a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> <span>Clients</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Paramètres</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h1>Tableau de Bord</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php 
                    $admin = getAdminInfo();
                    return;
                    echo strtoupper(substr($admin['username'], 0, 2)); 
                    ?>
                </div>
                <div>
                    <strong><?php echo htmlspecialchars($admin['username']); ?></strong>
                    <div style="font-size: 12px; color: #666;">Administrateur</div>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total_orders'] ?? 0; ?></div>
                <div class="stat-label">Commandes Total</div>
            </div>
            
            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_revenue'] ?? 0, 0, ',', ' '); ?> DZD</div>
                <div class="stat-label">Chiffre d'affaires</div>
            </div>
            
            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $stats['pending_orders'] ?? 0; ?></div>
                <div class="stat-label">En Attente</div>
            </div>
            
            <div class="stat-card products">
                <div class="stat-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total_products'] ?? 0; ?></div>
                <div class="stat-label">Produits</div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="recent-orders">
            <div class="section-title">
                <h3>Commandes Récentes</h3>
                <a href="orders.php" class="btn-view-all">Voir Tous</a>
            </div>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_orders->num_rows > 0): ?>
                            <?php while($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['nom_client']); ?></td>
                                <td><?php echo htmlspecialchars($order['telephone']); ?></td>
                                <td><?php echo number_format($order['total'], 0, ',', ' '); ?> DZD</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['date_commande'])); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch($order['statut']) {
                                        case 'en attente': $status_class = 'status-pending'; break;
                                        case 'confirmee': $status_class = 'status-confirmed'; break;
                                        case 'livree': $status_class = 'status-delivered'; break;
                                        default: $status_class = 'status-pending';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $order['statut']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" style="color: var(--primary); margin-right: 10px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                    Aucune commande trouvée
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="section-title">
                <h3>Actions Rapides</h3>
            </div>
            
            <div class="actions-grid">
                <a href="add_product.php" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <div>Ajouter Produit</div>
                </a>
                
                <a href="orders.php?status=en attente" class="action-btn">
                    <i class="fas fa-clock"></i>
                    <div>Voir Commandes En Attente</div>
                </a>
                
                <a href="customers.php" class="action-btn">
                    <i class="fas fa-user-plus"></i>
                    <div>Gérer Clients</div>
                </a>
                
                <a href="reports.php" class="action-btn">
                    <i class="fas fa-chart-bar"></i>
                    <div>Rapports</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>