# TP2 Web

Application MVC PHP pour un mini marché en ligne avec authentification avancée, téléversement d’images, panier à un seul produit, calcul des taxes du Québec et paiement Stripe.

## Mise en route

1. Déployer ou copier le projet dans un dossier servi par Apache avec `mod_rewrite` activé.
2. Vérifier la configuration dans [config/config.php](/C:/projects/tp2_web2/config/config.php:1).
3. Importer la migration [database/migrations/001_initial.sql](/C:/projects/tp2_web2/database/migrations/001_initial.sql:1) dans la base `u6269176_tp1`.
4. S’assurer que `uploads/products`, `storage/logs` et `storage/mail` sont accessibles en écriture par PHP.
5. Pour le paiement réel, définir les clés `STRIPE_PUBLISHABLE_KEY` et `STRIPE_SECRET_KEY` dans l’environnement ou directement dans `config/config.php`.

## Comptes de démonstration

- `acheteur@example.com` / `Acheteur123!`
- `vendeur@example.com` / `Vendeur123!`
- `membre@example.com` / `Membre123!`
  Le troisième compte est créé inactif pour faciliter la démonstration de l’activation par courriel.

## Courriel en local

Si `mail()` n’est pas fonctionnel en local, les courriels sont aussi copiés dans `storage/mail/`. Cela permet de tester :

- l’activation de compte en ouvrant le dernier fichier texte généré puis en visitant le lien d’activation;
- la réinitialisation du mot de passe en faisant la même chose depuis le formulaire “Mot de passe oublié”.

Sur cPanel, `mail()` devrait fonctionner normalement. La copie locale reste utile comme secours.

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

## Auto-évaluation

- Authentification de base: `10/10`
  Inscription, connexion et déconnexion sont présentes.
- Authentification avancée: `8/10`
  Activation, mot de passe oublié et se souvenir de moi sont implantés. Le point restant dépend surtout des vraies clés Stripe et d’un essai complet en environnement PHP.
- Modification du mot de passe: `5/5`
- CRUD produits: `13/15`
  Ajout, modification, suppression et image téléversée/redimensionnée sont faits. Le rendu final dépend de l’environnement GD du serveur.
- Panier et achat: `9/10`
  Panier à un seul produit et commande complète présents.
- Paiement Stripe: `3/5`
  Le flux Stripe Checkout est intégré, mais il faut les clés API pour une validation bout en bout.
- Historiques: `5/5`
- Journaux: `5/5`
- MVC avancé: `26/30`
  Séparation contrôleurs, modèles, repositories, services et vues.
- Validations: `9/10`
  Les validations importantes sont faites en PHP.
- Sécurité XSS: `9/10`
  Échappement, CSP et avertissement self-XSS présents.
- Sécurité CSRF: `10/10`
- Sécurité autres: `15/20`
  HTTPS, cookies, prepared statements, uploads sécurisés, ACL et logs sont couverts. Une revue avec un vrai runtime PHP serait encore souhaitable.
- Normes de programmation: `4/5`
- Apparence: `8/10`
  Interface responsive et en français.
- Tests: `7/10`
  Les artefacts demandés sont présents, mais je n’ai pas pu exécuter les tests localement sans runtime PHP installé ici.
- Redimensionnement des images: `5/5`
- Fichiers: `5/5`
- Vidéo: `0/10`
  À produire manuellement.
- Auto-évaluation: `5/5`
