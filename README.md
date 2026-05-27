# TP2 Web

Application MVC PHP pour un mini marché en ligne avec authentification avancée, téléversement d’images, panier à un seul produit, calcul des taxes du Québec et paiement Stripe Checkout.

## Mise en route

1. Déployer ou copier le projet dans un dossier servi par Apache avec `mod_rewrite` activé.
2. Copier [config/local.php.example](/C:/projects/tp2_web2/config/local.php.example:1) vers `config/local.php`.
3. Remplir `config/local.php` avec les vrais accès locaux:
   - base `u6269176_tp2`
   - utilisateur MySQL dédié au projet
   - clés Stripe si disponibles
4. Importer la migration [database/migrations/001_initial.sql](/C:/projects/tp2_web2/database/migrations/001_initial.sql:1) dans la base `u6269176_tp2`.
5. S’assurer que `uploads/products`, `storage/logs` et `storage/mail` sont accessibles en écriture par PHP.

Les vrais identifiants ne sont pas versionnés dans Git. `config/config.php` contient seulement des valeurs par défaut non sensibles et charge `config/local.php` si présent.

## Comptes de démonstration

- `acheteur@example.com` / `Acheteur123!`
- `vendeur@example.com` / `Vendeur123!`
- `vendeuse@example.com` / `Vendeur123!`
- `membre@example.com` / `Membre123!`
  Le dernier compte est créé inactif pour faciliter la démonstration de l’activation par courriel.

## Courriel en local

Si `mail()` n’est pas fonctionnel en local, les courriels sont aussi copiés dans `storage/mail/`. Cela permet de tester :

- l’activation de compte en ouvrant le dernier fichier texte généré puis en visitant le lien d’activation;
- la réinitialisation du mot de passe en faisant la même chose depuis le formulaire “Mot de passe oublié”.

## Paiement Stripe

Le flux en place utilise Stripe Checkout :

- le formulaire `/commande` collecte les informations personnelles, de facturation et de livraison;
- le paiement de la carte est saisi sur la page sécurisée Stripe;
- au retour, l’application vérifie le statut réel de la session Stripe avant de marquer la commande payée.

Sans clés Stripe valides, l’interface affiche clairement que le paiement réel n’est pas configuré.

## Tests

- Tests unitaires : [tests/run.php](/C:/projects/tp2_web2/tests/run.php:1)
- Gherkin : [tests/acceptance/achat_reussi.feature](/C:/projects/tp2_web2/tests/acceptance/achat_reussi.feature:1)
- Scénario PHP : [tests/acceptance/achat_reussi.php](/C:/projects/tp2_web2/tests/acceptance/achat_reussi.php:1)

Le dépôt inclut 5 tests unitaires, dont un test avec simulacre manuel dans `AuthServiceTest`.
