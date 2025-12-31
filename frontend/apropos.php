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
        <header class="hrader">
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
        <h1>A Propos de nous</h1>
        </header>
        <main class="apropos-container">
            <section class="apropos-section">
                <h2>Qui somme-nous ?</h2>
                <p>
                    Nous somme une boutique en ligne spécialisée dans la vente des ordinateurs portable modernes et peformants. <br>
                    Notre objectif est de proposer des produits fiables adapés aux besoins des étudiants, 
                    professionnels et passionnés d'informatique.
                </p>
            </section>
            <section class="apropos-section">
                <h2>Notre mission</h2>
                <p>
                    Faciliter l'accès à la technologie en Algérie en offrant des ordinateurs de qualité àdes prix raisonnables,
                    avec une expérience utilisateur simple et agréable.
                </p>
            </section>
            <section class="apropos-section">
                <h2>Pourquoi nous choisir?</h2>
                <ul>
                    <li>
                        Produit de qualité.
                    </li>
                    <li>
                        Prix adapté au marché algérien.
                    </li>
                    <li>
                        Site simple et modèrne.
                    </li>
                    <li>
                        Large choix d'ordinateur portables.
                    </li>
                </ul>
            </section>
            <section class="apropos-section">
                <h2>Notre engagement</h2>
                <p>
                    Nous nous engageons à améliorer continuellement nos services afin d'offrir la meilleure expérience
                    possible à nos clients.
                </p>
            </section>
        </main>
        <footer class="footer">
            <p>
                © 2025 - Boutiquetech21 | Tous droits réservés
            </p>
        </footer>
        <script src="script.js"></script>
    </body>
    </html>