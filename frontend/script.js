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

function afficherPanier() {
    const container = document.getElementById('panier-container');
    const totalSection = document.querySelector('.total');
    
    if (!container || !totalSection) return;
    const elementsProduits = container.querySelectorAll('.panier-produit, .panier-vide, p');
    elementsProduits.forEach(el => el.remove());
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
    totalSection.style.display = 'flex';
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
    const prixTotal = totalSection.querySelector('.prix-total');
    if (prixTotal) {
        prixTotal.textContent = formaterPrix(calculerTotal());
        prixTotal.style.color = '#7b3fe4';
        prixTotal.style.fontWeight = 'bold';
    }
}
function mettreAJourCompteurPanier() {
    const totalItems = panier.reduce((total, item) => total + item.quantite, 0);
    const liensPanier = document.querySelectorAll('a[href="panier.php"]');
    
    liensPanier.forEach(lien => {
        const ancienCompteur = lien.querySelector('.compteur-panier');
        if (ancienCompteur) ancienCompteur.remove();
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
function afficherNotification(message) { 
    const anciennesNotifs = document.querySelectorAll('.notification-pc-tech');
    anciennesNotifs.forEach(notif => notif.remove());
    const notification = document.createElement('div');
    notification.className = 'notification-pc-tech';
    notification.textContent = message;
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
    setTimeout(() => {
        notification.style.animation = 'slideOutNotif 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
function initialiserProduits() {
    const boutonsAcheter = document.querySelectorAll('.produit .acheter');
    boutonsAcheter.forEach(bouton => {
        bouton.addEventListener('click', function() {
            const produitDiv = this.closest('.produit');
            const nom = produitDiv.querySelector('h3').textContent;
            const prixText = produitDiv.querySelector('.prix').textContent;
            const image = produitDiv.querySelector('img').src;
            const prixMatch = prixText.match(/[\d\s]+/);
            if (prixMatch) {
                const prix = parseInt(prixMatch[0].replace(/\s/g, ''));
                ajouterAuPanier(nom, prix, image);
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
function initialiserEvenements() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-plus')) {
            const nom = e.target.dataset.nom;
            const produit = panier.find(item => item.nom === nom);
            if (produit) {
                modifierQuantite(nom, produit.quantite + 1);
                afficherNotification('Quantité augmentée pour ' + nom);
            }
        }
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
        if (e.target.classList.contains('btn-supprimer')) {
            const nom = e.target.dataset.nom;
            if (confirm(`Supprimer "${nom}" du panier?`)) {
                retirerDuPanier(nom);
                afficherNotification('🗑️ ' + nom + ' supprimé');
            }
        }
        if (e.target.classList.contains('continuer')) {
            if (panier.length === 0) {
                alert('Votre panier est vide. Ajoutez des produits avant de continuer.');
                return;
            }
            const total = calculerTotal();
            if (confirm(`Confirmer la commande pour ${formaterPrix(total)}?\n\nVous serez contacté pour finaliser la livraison.`)) {
                alert('✅ Commande confirmée! Merci pour votre confiance.\nNous vous contacterons dans les plus brefs délais.');
                panier = [];
                sauvegarderPanier();
                afficherPanier();
            }
        }
    });
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
function validerFormulaireContact() {
    const formulaire = document.querySelector('form');
    if (!formulaire) return;
   formulaire.addEventListener('submit', function(e) {
        e.preventDefault();
        const nom = this.querySelector('input[name="nom"]');
        const email = this.querySelector('input[name="email"]');
        const message = this.querySelector('textarea[name="message"]');
           let isValid = true;
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pc PortableTech - JavaScript chargé');
    if (document.querySelector('.produits')) {
        initialiserProduits();
    }
    if (document.getElementById('panier-container')) {
        afficherPanier();
    }
    initialiserEvenements();
    mettreAJourCompteurPanier();
    validerFormulaireContact();
 
});
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
function initialiserFormulaireContact() {
    const formulaire = document.getElementById('contactForm');
    const messageDiv = document.getElementById('formMessage');
      if (!formulaire) return;
      formulaire.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Envoi en cours...';
        submitBtn.disabled = true;
        const formData = new FormData(this);
        fetch('contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const [type, message] = data.split(':');
              if (type === 'success') {
                messageDiv.className = 'form-message success';
                messageDiv.textContent = message;
                formulaire.reset(); 
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = message;
            }
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
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
document.addEventListener('DOMContentLoaded', function() {
    initialiserFormulaireContact();
});
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
  console.log('Données à envoyer:', commandeData); 
    afficherNotification('📦 Enregistrement de la commande...');
    const url = '../backend/save_order.php';  
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
            alert(`🎉 Commande confirmée!
Numéro: #${data.order_id}
Total: ${formaterPrix(commandeData.total)}
Nous vous contacterons au ${telephone} pour la livraison.`);
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
function ajouterFormulaireCommande() {
    const panierContainer = document.getElementById('panier-container');
    if (!panierContainer) return;
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
    const totalSection = document.querySelector('.total');
    if (totalSection) {
        totalSection.parentNode.insertBefore(btnCommander, totalSection.nextSibling);
    }
}
function afficherModalCommande() {
    const ancienModal = document.querySelector('.modal-commande');
    if (ancienModal) ancienModal.remove();
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
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    `;
    document.head.appendChild(style);
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
    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    };
}
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('panier-container') && panier.length > 0) {
        ajouterFormulaireCommande();
    }
});
