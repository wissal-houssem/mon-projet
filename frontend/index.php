<?php 
// 1. إضافة كود الاتصال بقاعدة البيانات في أول السطر
include '../backend/config.php'; 
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
            <h4>Votre boutique en ligne de Pc portable</h4>
        </header>
        
        <section class="description">
            <p>
                Notre boutique en ligne est spécialisée dans la vente d'Pc portable
                de haute quality avec un service fiable.
                Découvrez nos modèles récents et performants pour tous vos besoins.
            </p>
        </section>
        <section class="contact">
               <p>☎ <a href="tel:+213795619662">+213 795 61 96 62</a></p>
        <P>✉ <a href="mailto:Boutiquetech21@gmail.com">Boutiquetech21@gmail.com</a></P>
        </section>
        <section class="localisation">
            <h3>Notre localisation</h3>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d204270.5898543412!2d6.639513360164634!3d36.87292975332807!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12f1c59c2432db4d%3A0x9406fc7f06be65d7!2sSkikda%2C%20Alg%C3%A9rie!5e0!3m2!1sfr!2sus!4v1766565529872!5m2!1sfr!2sus"
             width="600" height="450" style="border:0;" allowfullscreen="" 
            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </section>

<section class="contact-form-section">
    <div class="form-container">
        <h2>Contactez-nous</h2>
        <p>Une question? Envoyez-nous un message</p>
        
        <form id="contactForm" action="../backend/contact_handler.php" method="POST">
            <div class="form-group">
                <input type="text" name="nom" id="nom" placeholder="Votre nom complet" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Votre adresse email" required>
            </div>
            
            <div class="form-group">
                <input type="tel" name="telephone" id="telephone" placeholder="Votre numéro de téléphone">
            </div>
            
            <div class="form-group">
                <select name="sujet" id="sujet" required>
                    <option value="">Sélectionnez un sujet</option>
                    <option value="question">Question sur un produit</option>
                    <option value="commande">Suivi de commande</option>
                    <option value="technique">Support technique</option>
                    <option value="autre">Autre</option>
                </select>
            </div>
            
            <div class="form-group">
                <textarea name="message" id="message" rows="5" 
                          placeholder="Votre message..." required></textarea>
            </div>
            
            <button type="submit" class="submit-btn">
                <span>Envoyer le message</span>
                <span class="send-icon">✉️</span>
            </button>
        </form>
        
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="form-message success">Merci ! Votre message a été enregistré.</div>
        <?php endif; ?>
        
    </div>
</section>
        <script src="script.js"></script>
    </body>
</html>