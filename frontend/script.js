// ============================================
// GESTION DU PANIER (CART MANAGEMENT)
// ============================================

let panier = JSON.parse(localStorage.getItem('panier_pc_tech')) || [];

function sauvegarderPanier() {
    localStorage.setItem('panier_pc_tech', JSON.stringify(panier));
    mettreAJourCompteurPanier();
}

function ajouterAuPanier(nom, prix, image) {
    const produitExistant = panier.find(item => item.nom === nom);
    
    if (produitExistant) {
        produitExistant.quantite += 1;
    } else {
        panier.push({
            nom: nom,
            prix: prix,
            image: image,
            quantite: 1
        });
    }
    
    sauvegarderPanier();
    afficherNotification('✅ ' + nom + ' ajouté au panier');
}

function retirerDuPanier(nom) {
    panier = panier.filter(item => item.nom !== nom);
    sauvegarderPanier();
    if (document.getElementById('panier-container')) {
        afficherPanier();
    }
}

function modifierQuantite(nom, nouvelleQuantite) {
    if (nouvelleQuantite < 1) {
        retirerDuPanier(nom);
        return;
    }
    
    const produit = panier.find(item => item.nom === nom);
    if (produit) {
        produit.quantite = nouvelleQuantite;
        sauvegarderPanier();
        if (document.getElementById('panier-container')) {
            afficherPanier();
        }
    }
}

function calculerTotal() {
    return panier.reduce((total, item) => {
        return total + (item.prix * item.quantite);
    }, 0);
}

function formaterPrix(prix) {
    return prix.toLocaleString('fr-DZ') + ' DZD';
}

// ============================================
// AFFICHAGE DU PANIER
// ============================================

function afficherPanier() {
    const container = document.getElementById('panier-container');
    const totalSection = document.querySelector('.total');
    
    if (!container || !totalSection) return;
    
    // 1. تنظيف السلة فقط (لا تلمس .total)
    const elementsProduits = container.querySelectorAll('.panier-produit, .panier-vide, p');
    elementsProduits.forEach(el => el.remove());
    
    // 2. إذا السلة فارغة
    if (panier.length === 0) {
        const messageVide = document.createElement('div');
        messageVide.className = 'panier-vide';
        messageVide.style.cssText = `
            text-align: center;
            padding: 40px;
            background-color: #d5acf4;
            border-radius: 12px;
            margin: 20px 0;
            color: #000;
        `;
        
        messageVide.innerHTML = `
            <p style="font-size: 18px; margin-bottom: 20px;">Votre panier est vide</p>
            <a href="produits.php" style="
                background-color: #7b3fe4;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                display: inline-block;
            ">Voir nos produits</a>
        `;
        
        container.appendChild(messageVide);
        totalSection.style.display = 'none';
        return;
    }
    
    // 3. إظهار قسم المجموع
    totalSection.style.display = 'flex';
    
    
    // 4. عرض المنتجات
    panier.forEach(item => {
        const produitDiv = document.createElement('div');
        produitDiv.className = 'panier-produit';
        
        produitDiv.innerHTML = `
            <img src="${item.image}" alt="${item.nom}">
            <div style="flex-grow: 1;">
                <h3 style="margin: 0; color: #333;">${item.nom}</h3>
                <p style="color: #7b3fe4; font-weight: bold;">
                    Prix: ${formaterPrix(item.prix)} × ${item.quantite} = ${formaterPrix(item.prix * item.quantite)}
                </p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button class="btn-moins" data-nom="${item.nom}" style="
                        background-color: #c95bee;
                        color: black;
                        border: none;
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-weight: bold;
                    ">-</button>
                    
                    <span style="font-weight: bold;">${item.quantite}</span>
                    
                    <button class="btn-plus" data-nom="${item.nom}" style="
                        background-color: #c95bee;
                        color: black;
                        border: none;
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-weight: bold;
                    ">+</button>
                </div>
            </div>
            <button class="btn-supprimer" data-nom="${item.nom}" style="
                background-color: #e74c3c;
                color: black;
                border: none;
                padding: 8px 15px;
                border-radius: 6px;
                cursor: pointer;
                font-weight: bold;
                margin-left: 10px;
            ">Supprimer</button>
       ` ;
        
        container.appendChild(produitDiv);
    });
    
    // 5. تحديث المجموع الكلي
    const prixTotal = totalSection.querySelector('.prix-total');
    if (prixTotal) {
        prixTotal.textContent = formaterPrix(calculerTotal());
        prixTotal.style.color = '#7b3fe4';
        prixTotal.style.fontWeight = 'bold';
    }
}

// ============================================
// COMPTEUR PANIER
// ============================================

function mettreAJourCompteurPanier() {
    const totalItems = panier.reduce((total, item) => total + item.quantite, 0);
    
    // Mettre à jour le compteur dans la navigation
    const liensPanier = document.querySelectorAll('a[href="panier.php"]');
    
    liensPanier.forEach(lien => {
        // Supprimer l'ancien compteur
        const ancienCompteur = lien.querySelector('.compteur-panier');
        if (ancienCompteur) ancienCompteur.remove();
        
        // Ajouter nouveau compteur si nécessaire
        if (totalItems > 0) {
            const compteur = document.createElement('span');
            compteur.className = 'compteur-panier';
            compteur.textContent = totalItems;
            compteur.style.cssText = `
                background-color: #e74c3c;
                color: white;
                border-radius: 50%;
                padding: 2px 8px;
                font-size: 12px;
                margin-left: 5px;
                vertical-align: top;
           ` ;
            lien.appendChild(compteur);
        }
    });
}

// ============================================
// NOTIFICATIONS
// ============================================

function afficherNotification(message) {
    // Supprimer les anciennes notifications
    const anciennesNotifs = document.querySelectorAll('.notification-pc-tech');
    anciennesNotifs.forEach(notif => notif.remove());
    
    // Créer la notification
    const notification = document.createElement('div');
    notification.className = 'notification-pc-tech';
    notification.textContent = message;
    
    // Style selon votre design
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #d5acf4, #7b3fe4);
        color: black;
        padding: 15px 25px;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        font-weight: bold;
        border: 2px solid #c95bee;
        animation: slideInNotif 0.3s ease;
        max-width: 300px;
   ` ;
    
    // Ajouter l'animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInNotif {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutNotif {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOutNotif 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ============================================
// GESTION DES PRODUITS
// ============================================

function initialiserProduits() {
    const boutonsAcheter = document.querySelectorAll('.produit .acheter');
    
    boutonsAcheter.forEach(bouton => {
        bouton.addEventListener('click', function() {
            const produitDiv = this.closest('.produit');
            const nom = produitDiv.querySelector('h3').textContent;
            const prixText = produitDiv.querySelector('.prix').textContent;
            const image = produitDiv.querySelector('img').src;
            
            // Extraire le prix (supporte le format "120 000 DZD")
            const prixMatch = prixText.match(/[\d\s]+/);
            if (prixMatch) {
                const prix = parseInt(prixMatch[0].replace(/\s/g, ''));
                ajouterAuPanier(nom, prix, image);
                
                // Effet visuel sur le bouton
                this.style.backgroundColor = '#5750d4';
                this.textContent = '✓ Ajouté';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                    this.textContent = 'Acheter';
                }, 1000);
            }
        });
    });
}

// ============================================
// GESTION DES ÉVÉNEMENTS
// ============================================

function initialiserEvenements() {
    // Gestion des clics sur le panier
    document.addEventListener('click', function(e) {
        // Bouton +
        if (e.target.classList.contains('btn-plus')) {
            const nom = e.target.dataset.nom;
            const produit = panier.find(item => item.nom === nom);
            if (produit) {
                modifierQuantite(nom, produit.quantite + 1);
                afficherNotification('Quantité augmentée pour ' + nom);
            }
        }
        
        // Bouton -
        if (e.target.classList.contains('btn-moins')) {
            const nom = e.target.dataset.nom;
            const produit = panier.find(item => item.nom === nom);
            if (produit && produit.quantite > 1) {
                modifierQuantite(nom, produit.quantite - 1);
                afficherNotification('Quantité réduite pour ' + nom);
            } else if (produit) {
                retirerDuPanier(nom);
            }
        }
        
        // Bouton Supprimer
        if (e.target.classList.contains('btn-supprimer')) {
            const nom = e.target.dataset.nom;
            if (confirm(`Supprimer "${nom}" du panier?`)) {
                retirerDuPanier(nom);
                afficherNotification('🗑️ ' + nom + ' supprimé');
            }
        }
        
        // Bouton Continuer
        if (e.target.classList.contains('continuer')) {
            if (panier.length === 0) {
                alert('Votre panier est vide. Ajoutez des produits avant de continuer.');
                return;
            }
            
            const total = calculerTotal();
            if (confirm(`Confirmer la commande pour ${formaterPrix(total)}?\n\nVous serez contacté pour finaliser la livraison.`)) {
                alert('✅ Commande confirmée! Merci pour votre confiance.\nNous vous contacterons dans les plus brefs délais.');
                
                // Vider le panier après confirmation
                panier = [];
                sauvegarderPanier();
                afficherPanier();
                
                // Redirection (optionnelle)
                // window.location.href = 'index.html';
            }
        }
    });
    
    // Ajouter bouton "Vider le panier" si on est sur la page panier
    if (document.getElementById('panier-container') && panier.length > 0) {
        const totalSection = document.querySelector('.total');
        if (totalSection && !document.querySelector('.btn-vider')) {
            const btnVider = document.createElement('button');
            btnVider.className = 'btn-vider';
            btnVider.textContent = 'Vider le panier';
            btnVider.style.cssText = `
                background-color: #e74c3c;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 10px 20px;
                cursor: pointer;
                font-weight: bold;
                margin-left: 15px;
                transition: background-color 0.3s;
           ` ;
            btnVider.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#c0392b';
            });
            btnVider.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '#e74c3c';
            });
            
            btnVider.addEventListener('click', function() {
                if (confirm('Voulez-vous vider tout le panier?')) {
                    panier = [];
                    sauvegarderPanier();
                    afficherPanier();
                    afficherNotification('Panier vidé');
                }
            });
            
            totalSection.appendChild(btnVider);
        }
    }
}

// ============================================
// FORMULAIRE DE CONTACT (si ajouté plus tard)
// ============================================

function validerFormulaireContact() {
    const formulaire = document.querySelector('form');
    if (!formulaire) return;
    
    formulaire.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nom = this.querySelector('input[name="nom"]');
        const email = this.querySelector('input[name="email"]');
        const message = this.querySelector('textarea[name="message"]');
        
        let isValid = true;
        
        // Validation basique
        if (nom && nom.value.trim() === '') {
            isValid = false;
            nom.style.borderColor = '#e74c3c';
        } else if (nom) {
            nom.style.borderColor = '#7b3fe4';
        }
        
        if (email && !email.value.includes('@')) {
            isValid = false;
            email.style.borderColor = '#e74c3c';
        } else if (email) {
            email.style.borderColor = '#7b3fe4';
        }
        
        if (message && message.value.trim() === '') {
            isValid = false;
            message.style.borderColor = '#e74c3c';
        } else if (message) {
            message.style.borderColor = '#7b3fe4';
        }
        
        if (isValid) {
            afficherNotification('📧 Message envoyé avec succès!');
            this.reset();
        } else {
            afficherNotification('❌ Veuillez remplir tous les champs correctement');
        }
    });
}

// ============================================
// INITIALISATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pc PortableTech - JavaScript chargé');
    
    // Initialiser les produits sur la page produits.html
    if (document.querySelector('.produits')) {
        initialiserProduits();
    }
    
    // Afficher le panier sur la page panier.html
    if (document.getElementById('panier-container')) {
        afficherPanier();
    }
    
    // Initialiser les événements
    initialiserEvenements();
    
    // Mettre à jour le compteur
    mettreAJourCompteurPanier();
    
    // Validation formulaire (si existant)
    validerFormulaireContact();
 
});

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

// Exposer certaines fonctions globalement (pour la console debug)
window.monPanier = {
    getPanier: () => panier,
    viderPanier: () => {
        panier = [];
        sauvegarderPanier();
        afficherPanier();
        afficherNotification('Panier vidé');
    },
    ajouterTest: (nom = 'HP Test', prix = 100000) => {
        ajouterAuPanier(nom, prix, '');
    }
};
// ============================================
// GESTION DU FORMULAIRE DE CONTACT
// ============================================

function initialiserFormulaireContact() {
    const formulaire = document.getElementById('contactForm');
    const messageDiv = document.getElementById('formMessage');
    
    if (!formulaire) return;
    
    formulaire.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Désactiver le bouton pendant l'envoi
        const submitBtn = this.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Envoi en cours...';
        submitBtn.disabled = true;
        
        // Récupérer les données du formulaire
        const formData = new FormData(this);
        
        // Envoyer avec fetch (AJAX)
        fetch('contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Analyser la réponse
            const [type, message] = data.split(':');
            
            if (type === 'success') {
                messageDiv.className = 'form-message success';
                messageDiv.textContent = message;
                formulaire.reset(); // Réinitialiser le formulaire
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = message;
            }
            
            // Réactiver le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Cacher le message après 5 secondes
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                setTimeout(() => {
                    messageDiv.className = 'form-message';
                    messageDiv.style.opacity = '1';
                }, 500);
            }, 5000);
        })
        .catch(error => {
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'Erreur de connexion. Veuillez réessayer.';
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Ajouter à l'initialisation
document.addEventListener('DOMContentLoaded', function() {
    initialiserFormulaireContact();
});
// ============================================
// ENREGISTRER COMMANDE
// ============================================

// ============================================
// ENREGISTRER COMMANDE (نسخة معدلة)
// ============================================

function enregistrerCommande(nomClient, telephone, adresse) {
    if (!nomClient || !telephone || !adresse) {
        afficherNotification('❌ Veuillez remplir tous les champs');
        return false;
    }

    const commandeData = {
        nom_client: nomClient,
        telephone: telephone,
        adresse: adresse,
        produits: panier,
        total: calculerTotal()
    };

    console.log('Données à envoyer:', commandeData); // للتتبع
    
    // إظهار مؤشر التحميل
    afficherNotification('📦 Enregistrement de la commande...');

    // إصلاح المسار - تأكد من أنه صحيح
    const url = '../backend/save_order.php';  // جرب هذا المسار
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(commandeData)
    })
    .then(response => {
        console.log('Statut réponse:', response.status);
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse du serveur:', data);
        if (data.success) {
            afficherNotification(`✅ Commande #${data.order_id} enregistrée!`);
            
            // إظهار تفاصيل الطلب
            alert(`🎉 Commande confirmée!
Numéro: #${data.order_id}
Total: ${formaterPrix(commandeData.total)}
Nous vous contacterons au ${telephone} pour la livraison.`);
            
            // تفريغ السلة
            panier = [];
            sauvegarderPanier();
            afficherPanier();
        } else {
            afficherNotification(`❌ Erreur: ${data.error}`);
        }
    })
    .catch(error => {
        console.error('Erreur détaillée:', error);
        afficherNotification('❌ Erreur de connexion: ' + error.message);
    });
}
// ============================================
// AJOUTER UN FORMULAIRE POUR LA COMMANDE
// ============================================

function ajouterFormulaireCommande() {
    const panierContainer = document.getElementById('panier-container');
    if (!panierContainer) return;
    
    // إضافة زر "Passer la commande"
    const btnCommander = document.createElement('button');
    btnCommander.id = 'btn-commander';
    btnCommander.textContent = 'Passer la commande';
    btnCommander.style.cssText = `
        background: linear-gradient(135deg, #7b3fe4, #c95bee);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 15px 30px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        margin: 20px auto;
        display: block;
        transition: all 0.3s;
    `;
    
    btnCommander.onmouseover = () => btnCommander.style.transform = 'translateY(-3px)';
    btnCommander.onmouseout = () => btnCommander.style.transform = 'translateY(0)';
    
    btnCommander.onclick = () => {
        afficherModalCommande();
    };
    
    // إضافة بعد قسم المجموع
    const totalSection = document.querySelector('.total');
    if (totalSection) {
        totalSection.parentNode.insertBefore(btnCommander, totalSection.nextSibling);
    }
}

// ============================================
// MODAL POUR LA COMMANDE
// ============================================

function afficherModalCommande() {
    // إزالة أي مودال موجود
    const ancienModal = document.querySelector('.modal-commande');
    if (ancienModal) ancienModal.remove();
    
    // إنشاء المودال
    const modal = document.createElement('div');
    modal.className = 'modal-commande';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        animation: fadeIn 0.3s;
    `;
    
    // محتوى المودال
    modal.innerHTML = `
        <div style="
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        ">
            <h2 style="color: #d5acf4; text-align: center; margin-bottom: 20px;">
                Informations de livraison
            </h2>
            
            <div style="margin-bottom: 20px;">
                <input type="text" id="nom-client" placeholder="Votre nom complet" 
                    style="width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <input type="tel" id="telephone" placeholder="Numéro de téléphone" 
                    style="width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <textarea id="adresse" placeholder="Adresse de livraison complète" rows="4"
                    style="width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none;"></textarea>
            </div>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button id="btn-confirmer" style="
                    background: linear-gradient(135deg, #4CAF50, #2E7D32);
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: bold;
                ">Confirmer</button>
                
                <button id="btn-annuler" style="
                    background: #e74c3c;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: bold;
                ">Annuler</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // إضافة الأنيميشن
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    `;
    document.head.appendChild(style);
    
    // الأحداث
    document.getElementById('btn-confirmer').onclick = () => {
        const nomClient = document.getElementById('nom-client').value;
        const telephone = document.getElementById('telephone').value;
        const adresse = document.getElementById('adresse').value;
        
        if (nomClient && telephone && adresse) {
            enregistrerCommande(nomClient, telephone, adresse);
            modal.remove();
        } else {
            afficherNotification('❌ Veuillez remplir tous les champs');
        }
    };
    
    document.getElementById('btn-annuler').onclick = () => {
        modal.remove();
    };
    
    // إغلاق بالنقر خارج المودال
    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    };
}

// ============================================
// MODIFIER INITIALISATION
// ============================================

// في نهاية DOMContentLoaded، أضف:
document.addEventListener('DOMContentLoaded', function() {
    // ... الكود الحالي ...
    
    // إضافة زر "Passer la commande" إذا كانت الصفحة panier
    if (document.getElementById('panier-container') && panier.length > 0) {
        ajouterFormulaireCommande();
    }
});