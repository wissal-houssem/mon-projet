<?php
// backend/orders.php
require_once 'session.php';
require_once 'config.php';
requireAdminLogin();

// البحث والتصفية
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// بناء الاستعلام
$sql = "SELECT * FROM commandes WHERE 1=1";
$params = [];
$types = "";

if (!empty($status_filter)) {
    $sql .= " AND statut = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (nom_client LIKE ? OR telephone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$sql .= " ORDER BY date_commande DESC";

// إعداد الاستعلام
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// إحصائيات
$stats_sql = "SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN statut = 'en attente' THEN 1 END) as pending,
    COUNT(CASE WHEN statut = 'confirmee' THEN 1 END) as confirmed,
    COUNT(CASE WHEN statut = 'livree' THEN 1 END) as delivered,
    SUM(total) as revenue
    FROM commandes";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* إضافة نفس الـCSS من dashboard */
        .orders-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        .filter-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .order-actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-confirm { background: #2ecc71; color: white; }
        .btn-deliver { background: #3498db; color: white; }
        .btn-cancel { background: #e74c3c; color: white; }
        .btn-view { background: #9b59b6; color: white; }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .page-link {
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .page-link.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <!-- نفس الـSidebar من dashboard -->
    
    <div class="main-content">
        <div class="topbar">
            <h1>Gestion des Commandes</h1>
            <div class="user-info">
                <!-- نفس المستخدم -->
            </div>
        </div>
        
        <!-- Filters -->
        <div class="orders-container">
            <div class="filters">
                <div class="filter-group">
                    <input type="text" id="search" placeholder="Rechercher par client ou téléphone..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-group">
                    <select id="status">
                        <option value="">Tous les statuts</option>
                        <option value="en attente" <?php echo $status_filter == 'en attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="confirmee" <?php echo $status_filter == 'confirmee' ? 'selected' : ''; ?>>Confirmée</option>
                        <option value="livree" <?php echo $status_filter == 'livree' ? 'selected' : ''; ?>>Livrée</option>
                    </select>
                </div>
                
                <button class="filter-btn" onclick="applyFilters()">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                
                <button class="filter-btn" onclick="resetFilters()" style="background: #666;">
                    <i class="fas fa-redo"></i> Réinitialiser
                </button>
            </div>
            
            <!-- Statistics -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--primary);">
                        <?php echo $stats['total']; ?>
                    </div>
                    <div>Total</div>
                </div>
                
                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #856404;">
                        <?php echo $stats['pending']; ?>
                    </div>
                    <div>En Attente</div>
                </div>
                
                <div style="background: #d4edda; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #155724;">
                        <?php echo $stats['confirmed']; ?>
                    </div>
                    <div>Confirmées</div>
                </div>
                
                <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #0c5460;">
                        <?php echo $stats['delivered']; ?>
                    </div>
                    <div>Livrées</div>
                </div>
                
                <div style="background: #e2e3e5; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #383d41;">
                        <?php echo number_format($stats['revenue'], 0, ',', ' '); ?> DZD
                    </div>
                    <div>Revenu Total</div>
                </div>
            </div>
            
            <!-- Orders Table -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Adresse</th>
                            <th>Produits</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($order = $result->fetch_assoc()): 
                                $products = json_decode($order['produits'], true);
                            ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['nom_client']); ?></td>
                                <td><?php echo htmlspecialchars($order['telephone']); ?></td>
                                <td title="<?php echo htmlspecialchars($order['adresse']); ?>">
                                    <?php echo substr($order['adresse'], 0, 20) . '...'; ?>
                                </td>
                                <td>
                                    <?php if (is_array($products)): ?>
                                        <?php echo count($products); ?> produit(s)
                                    <?php else: ?>
                                        0 produit
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($order['total'], 0, ',', ' '); ?> DZD</td>
                                <td><?php echo date('d/m/Y', strtotime($order['date_commande'])); ?></td>
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
                                    <div class="order-actions">
                                        <button class="btn-action btn-view" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($order['statut'] == 'en attente'): ?>
                                            <button class="btn-action btn-confirm" onclick="updateStatus(<?php echo $order['id']; ?>, 'confirmee')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php elseif ($order['statut'] == 'confirmee'): ?>
                                            <button class="btn-action btn-deliver" onclick="updateStatus(<?php echo $order['id']; ?>, 'livree')">
                                                <i class="fas fa-truck"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button class="btn-action btn-cancel" onclick="updateStatus(<?php echo $order['id']; ?>, 'annulee')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 30px; color: #666;">
                                    Aucune commande trouvée
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    function applyFilters() {
        const search = document.getElementById('search').value;
        const status = document.getElementById('status').value;
        
        let url = 'orders.php?';
        if (search) url += 'search=' + encodeURIComponent(search) + '&';
        if (status) url += 'status=' + encodeURIComponent(status);
        
        window.location.href = url;
    }
    
    function resetFilters() {
        window.location.href = 'orders.php';
    }
    
    function viewOrder(id) {
        window.location.href = 'order_details.php?id=' + id;
    }
    
    function updateStatus(orderId, status) {
        if (confirm('Êtes-vous sûr de vouloir changer le statut de cette commande?')) {
            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'order_id=' + orderId + '&status=' + status
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Statut mis à jour avec succès!');
                    location.reload();
                } else {
                    alert('Erreur: ' + data.error);
                }
            })
            .catch(error => {
                alert('Erreur de connexion');
            });
        }
    }
    </script>
</body>
</html>
<?php 
$stmt->close();
$conn->close();
?>