<?php
// backend/admin_orders.php
require_once 'config.php';

// للتحقق من الوصول (يمكنك إضافة نظام تسجيل دخول لاحقاً)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Commandes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #1a1a2e; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #d5acf4; margin-bottom: 30px; text-align: center; }
        .order-card { 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-left: 5px solid #7b3fe4;
        }
        .order-header { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 15px; 
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .order-id { color: #c95bee; font-weight: bold; font-size: 18px; }
        .order-date { color: #aaa; }
        .order-total { color: #4CAF50; font-weight: bold; font-size: 20px; }
        .customer-info { margin-bottom: 15px; }
        .customer-info p { margin: 5px 0; }
        .products-list { 
            background: rgba(0,0,0,0.3); 
            padding: 15px; 
            border-radius: 8px; 
            margin-top: 10px;
        }
        .product-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .product-item:last-child { border-bottom: none; }
        .status { 
            display: inline-block; 
            padding: 5px 15px; 
            border-radius: 20px; 
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .en-attente { background: #ff9800; color: black; }
        .confirmee { background: #4CAF50; color: white; }
        .livree { background: #2196F3; color: white; }
        .annulee { background: #f44336; color: white; }
        
        .actions { margin-top: 15px; }
        .btn { 
            padding: 8px 15px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin-right: 10px;
            font-weight: bold;
        }
        .btn-confirmer { background: #4CAF50; color: white; }
        .btn-livrer { background: #2196F3; color: white; }
        .btn-annuler { background: #f44336; color: white; }
        
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px;
        }
        .stat-card { 
            background: rgba(213, 172, 244, 0.2); 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center;
        }
        .stat-value { font-size: 36px; font-weight: bold; color: #d5acf4; }
        .stat-label { color: #aaa; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Commandes Clients - Administration</h1>
        
        <?php
        // استعلام للحصول على إحصائيات
        $stats_sql = "SELECT 
            COUNT(*) as total_orders,
            SUM(total) as total_revenue,
            AVG(total) as avg_order,
            COUNT(CASE WHEN statut = 'en attente' THEN 1 END) as pending_orders
            FROM commandes";
        
        $stats_result = $conn->query($stats_sql);
        $stats = $stats_result->fetch_assoc();
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                <div class="stat-label">Commandes Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?> DZD</div>
                <div class="stat-label">Chiffre d'affaires</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['avg_order'], 0, ',', ' '); ?> DZD</div>
                <div class="stat-label">Moyenne par commande</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
                <div class="stat-label">En attente</div>
            </div>
        </div>
        
        <?php
        // استعلام للحصول على جميع الطلبات
        $sql = "SELECT * FROM commandes ORDER BY date_commande DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($order = $result->fetch_assoc()) {
                $products = json_decode($order['produits'], true);
        ?>
        
        <div class="order-card">
            <div class="order-header">
                <div>
                    <span class="order-id">Commande #<?php echo $order['id']; ?></span>
                    <span class="order-date"> - <?php echo date('d/m/Y H:i', strtotime($order['date_commande'])); ?></span>
                </div>
                <div class="order-total"><?php echo number_format($order['total'], 0, ',', ' '); ?> DZD</div>
            </div>
            
            <div class="customer-info">
                <p><strong>👤 Client:</strong> <?php echo htmlspecialchars($order['nom_client']); ?></p>
                <p><strong>📞 Téléphone:</strong> <?php echo htmlspecialchars($order['telephone']); ?></p>
                <p><strong>📍 Adresse:</strong> <?php echo htmlspecialchars($order['adresse']); ?></p>
            </div>
            
            <div class="products-list">
                <h4>Produits commandés:</h4>
                <?php if (is_array($products)): ?>
                    <?php foreach($products as $product): ?>
                    <div class="product-item">
                        <span><?php echo htmlspecialchars($product['nom']); ?></span>
                        <span><?php echo number_format($product['prix'], 0, ',', ' '); ?> DZD × <?php echo $product['quantite']; ?></span>
                        <span><strong><?php echo number_format($product['prix'] * $product['quantite'], 0, ',', ' '); ?> DZD</strong></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Produits non disponibles</p>
                <?php endif; ?>
            </div>
            
            <div class="status <?php echo str_replace(' ', '-', $order['statut']); ?>">
                Statut: <?php echo $order['statut']; ?>
            </div>
            
            <div class="actions">
                <form method="POST" action="update_order_status.php" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" name="status" value="confirmee" class="btn btn-confirmer">Confirmer</button>
                    <button type="submit" name="status" value="livree" class="btn btn-livrer">Marquer comme livrée</button>
                    <button type="submit" name="status" value="annulee" class="btn btn-annuler">Annuler</button>
                </form>
            </div>
        </div>
        
        <?php
            }
        } else {
            echo '<div style="text-align: center; padding: 40px; background: rgba(255,0,0,0.1); border-radius: 10px;">
                    <h3 style="color: #ff6b6b;">Aucune commande trouvée</h3>
                    <p>Les commandes apparaîtront ici quand les clients passeront commande.</p>
                  </div>';
        }
        
        $conn->close();
        ?>
    </div>
</body>
</html>