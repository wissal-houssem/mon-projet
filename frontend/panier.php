<?php
include '../backend/config.php'
?>



<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Mon Site Web</title>
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
<h1>Pc PortableTech</h1>
        </header>
        <main>
            <h2>Votre Panier</h2><br>
        <div id="panier-container">
        <div class="total">
            <Span>Total :</Span>
            <span class="prix-total">Total sera calculé plus tard</span>
            <button class="continuer">Continuer</button>
        </div>
        </main>
        <script src="script.js"></script>
    </body>
</html>