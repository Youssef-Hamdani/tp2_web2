# TP2 Web

Application MVC PHP pour un mini marché en ligne avec authentification avancée, téléversement d'images, panier à un seul produit, calcul des taxes du Québec et paiement Stripe Checkout.

## Mise en route en Docker

Prérequis :
- Docker
- le port `8080` disponible

Étapes :
1. Dans la racine du projet, lancer `docker compose up -d --build`.
2. Attendre que les conteneurs `app` et `db` soient en état `healthy` ou `running`.
3. Ouvrir [http://localhost:8080](http://localhost:8080).

Commandes utiles :
- voir les logs : `docker compose logs -f`
- lancer les tests unitaires : `docker compose exec app php tests/run.php`

## Mise en route sans Docker

1. Déployer ou copier le projet dans un dossier servi par Apache avec `mod_rewrite` activé.
2. Vérifier [config/config.php](./config/config.php) pour :
   - `app.base_url`
   - les accès à la base de données
   - les clés Stripe
3. Importer la migration [database/migrations/001_initial.sql](./database/migrations/001_initial.sql) dans la base `u6269176_tp2`.
4. S'assurer que `uploads/products`, `storage/logs` et `storage/mail` sont accessibles en écriture par PHP.
5. Pointer Apache vers [index.php](./index.php) avec les beaux URLs activés via [.htaccess](./.htaccess).

## Comptes de démonstration

- `acheteur@example.com` / `Acheteur123!`
- `vendeur@example.com` / `Vendeur123!`
- `vendeuse@example.com` / `Vendeur123!`
- `membre@example.com` / `Membre123!`

Le dernier compte est créé inactif pour faciliter la démonstration de l'activation par courriel.

## Courriel en local

Si `mail()` ne fonctionne pas en local, les courriels sont aussi copiés dans `storage/mail/`.

Pour tester l'activation :
- créer un compte
- ouvrir le dernier fichier généré dans `storage/mail/`
- visiter le lien d'activation contenu dans le courriel

Pour tester la réinitialisation du mot de passe :
- utiliser le formulaire "Mot de passe oublié"
- ouvrir le dernier fichier généré dans `storage/mail/`
- visiter le lien de réinitialisation contenu dans le courriel

## Paiement Stripe

Le flux utilise Stripe Checkout :
- le formulaire `/commande` collecte les informations personnelles, de facturation et de livraison
- la carte est saisie sur la page Stripe
- au retour, l'application vérifie le statut réel de la session Stripe avant de marquer la commande payée

En Docker local, les clés Stripe sont laissées vides par défaut dans `docker-compose.yml`. Le site fonctionne quand même pour le reste, mais le paiement réel n'est pas activé tant que les variables Stripe ne sont pas remplies.

## Tests

- Tests unitaires : [tests/run.php](./tests/run.php)
- Gherkin : [tests/acceptance/achat_reussi.feature](./tests/acceptance/achat_reussi.feature)
- Scénario PHP : [tests/acceptance/achat_reussi.php](./tests/acceptance/achat_reussi.php)

Le dépôt inclut 6 tests unitaires qui passent dans Docker avec `php tests/run.php`.

## Auto-évaluation

### Fonctionnalités

| Élément | Points | Auto-évaluation | Note |
| --- | ---: | --- | ---: |
| Authentification de base (1.1, 1.2 et 2.1) | 10 | Inscription, connexion et déconnexion fonctionnelles | 10 |
| Authentification avancée (1.3, 1.4 et 5.8) | 10 | Activation, mot de passe oublié, se souvenir de moi, envoi de courriels | 10 |
| Modification du mot de passe (2.2) | 5 | Fonctionnelle avec validation PHP | 5 |
| CRUD produits (1.5, 4.1, 4.2 et 5.2) | 15 | Ajout, modification, suppression des produits non vendus, image et frais de service | 15 |
| Panier et achat (3.1, 3.2 et 5.1) | 10 | Panier à un seul produit, taxes du Québec, informations personnelles/facturation/livraison | 10 |
| Paiement Stripe (5.7) | 5 | Paiement Stripe Checkout fonctionnel en mode test | 5 |
| Historiques (3.3 et 4.3) | 5 | Historique des achats et des ventes présent | 5 |
| Journaux (5.3) | 5 | Journal des ventes présent en base | 5 |

Sous-total fonctionnalités : **70 / 70**

### Code

| Élément | Maximum | Auto-évaluation | Note |
| --- | ---: | --- | ---: |
| MVC avancé | 30 | Architecture MVC séparée correctement, mais il reste quelques simplifications maison | 24 |
| Validations | 10 | Validations côté PHP sur les formulaires principaux | 10 |
| Sécurité, XSS | 10 | Sortie échappée et usage de `innerText`, mais je reste prudent sur un oubli possible | 8 |
| Sécurité, CSRF | 10 | Jetons CSRF sur les formulaires POST selon la technique demandée | 10 |
| Sécurité, autres | 20 | HTTPS, cookies de session, ACL, CSP, upload valide, requêtes préparées, mais je reste conservateur | 12 |
| Normes de programmation | 5 | Code globalement uniforme, mais quelques textes/encodages pourraient être plus propres | 3 |
| Apparence (ergonomie, réactivité et accessibilité) | 10 | Interface fonctionnelle et responsive, mais visuellement simple et perfectible | 7 |
| Tests (5.5) | 10 | Exigence minimale atteinte et tests exécutés en Docker, mais couverture encore limitée | 6 |
| Redimensionnement des images (5.6) | 5 | Fait | 5 |
| Fichiers | 5 | Migration, README, Docker et dépôt propres | 5 |

Sous-total code : **90 / 115**

### Autre

| Élément | Maximum | Auto-évaluation | Note |
| --- | ---: | --- | ---: |
| Vidéo | 10 | À fournir séparément | 0 |
| Auto-évaluation | 5 | Présentée ici et détaillée | 5 |

Sous-total autre : **5 / 15**

## Total estimé

- Fonctionnalités : **70 / 70**
- Code : **90 / 115**
- Autre : **5 / 15**
- Total estimé actuel : **165 / 200**

Si la vidéo est fournie correctement, le total estimé devient **175 / 200**.

## Notes

- Le site est déployé sur cPanel et testable en ligne.
- Le paiement Stripe a été vérifié en mode test.
- L'interface est en français, mais quelques accents/termes restent à polir pour maximiser la présentation.
