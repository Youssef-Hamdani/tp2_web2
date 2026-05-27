<?php

declare(strict_types=1);

/**
 * Test d'acceptation de référence.
 *
 * Parcours visé :
 * 1. Créer ou utiliser un acheteur actif.
 * 2. Se connecter.
 * 3. Ajouter un produit disponible au panier.
 * 4. Remplir les informations personnelles, de facturation et de livraison.
 * 5. Être redirigé vers Stripe et terminer le paiement.
 * 6. Vérifier ensuite :
 *    - le statut du produit = sold;
 *    - une commande paid existe;
 *    - l'achat apparaît dans /compte/achats;
 *    - la vente apparaît dans /compte/ventes;
 *    - un enregistrement existe dans sales_logs et storage/logs/application.log.
 *
 * Ce fichier sert de scénario PHP lisible à exécuter manuellement ou à convertir
 * en test automatisé HTTP lorsque l'environnement PHP complet est disponible.
 */
