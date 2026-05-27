# TP2 Web

Application MVC PHP pour un mini marche en ligne avec authentification avancee, televersement d'images, panier a un seul produit, calcul des taxes du Quebec et paiement Stripe Checkout.

## Mise en route

1. Deployer ou copier le projet dans un dossier servi par Apache avec `mod_rewrite` active.
2. Verifier [config/config.php](/C:/projects/tp2_web2/config/config.php:1) pour les acces BD, les cles Stripe et `BASE_URL`.
3. Importer la migration [database/migrations/001_initial.sql](/C:/projects/tp2_web2/database/migrations/001_initial.sql:1) dans la base `u6269176_tp2`.
4. S'assurer que `uploads/products`, `storage/logs` et `storage/mail` sont accessibles en ecriture par PHP.

## Comptes de demonstration

- `acheteur@example.com` / `Acheteur123!`
- `vendeur@example.com` / `Vendeur123!`
- `vendeuse@example.com` / `Vendeur123!`
- `membre@example.com` / `Membre123!`
  Le dernier compte est cree inactif pour faciliter la demonstration de l'activation par courriel.

## Courriel en local

Si `mail()` n'est pas fonctionnel en local, les courriels sont aussi copies dans `storage/mail/`. Cela permet de tester :

- l'activation de compte en ouvrant le dernier fichier texte genere puis en visitant le lien d'activation;
- la reinitialisation du mot de passe en faisant la meme chose depuis le formulaire "Mot de passe oublie".

## Paiement Stripe

Le flux en place utilise Stripe Checkout :

- le formulaire `/commande` collecte les informations personnelles, de facturation et de livraison;
- le paiement de la carte est saisi sur la page securisee Stripe;
- au retour, l'application verifie le statut reel de la session Stripe avant de marquer la commande payee.

## Tests

- Tests unitaires : [tests/run.php](/C:/projects/tp2_web2/tests/run.php:1)
- Gherkin : [tests/acceptance/achat_reussi.feature](/C:/projects/tp2_web2/tests/acceptance/achat_reussi.feature:1)
- Scenario PHP : [tests/acceptance/achat_reussi.php](/C:/projects/tp2_web2/tests/acceptance/achat_reussi.php:1)

Le depot inclut 5 tests unitaires, dont un test avec simulacre manuel dans `AuthServiceTest`.
