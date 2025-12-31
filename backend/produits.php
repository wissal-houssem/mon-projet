<?php
require_once 'config.php';
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Produits pc portableTech</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
         <header>
        <nav>
            <ul>
                <li>
                    <a href="index.php">Accueil</a>
                </li>
                <li>
                    <a href="produits.php">Produits</a>
                </li>
                <li>
                    <a href="panier.php">Acheter</a>
                </li>
                <li>
                    <a href="apropos.php">A propos</a>
                </li>
            </ul>
        </nav>
<h1>Pc PortableTech</h1> <br>
     </header>
     <main>
     <h4>Nos Pc Portable</h4>
     <section class="produits">
    <?php if ($result->num_rows > 0): ?>
        <?php while($produit = $result->fetch_assoc()): ?>
        <div class="produit">
            <img src="<?php echo $produit['image']; ?>" alt="<?php echo $produit['nom']; ?>">
            <h3><?php echo $produit['nom']; ?></h3>
            <p><?php echo $produit['description']; ?></p>
            <p class="prix">Prix: <?php echo number_format($produit['prix'], 0, ',', ' '); ?> DZD</p>
            <button class="acheter" 
                    data-id="<?php echo $produit['id']; ?>"
                    data-nom="<?php echo $produit['nom']; ?>"
                    data-prix="<?php echo $produit['prix']; ?>"
                    data-image="<?php echo $produit['image']; ?>">
                Acheter
            </button>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucun produit disponible</p>
    <?php endif; ?>
    
    <?php $conn->close(); ?>
</section>
     </main>
     <script src="script.js"></script>
    </body>

</html>