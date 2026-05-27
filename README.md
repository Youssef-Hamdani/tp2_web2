# TP2 Web

Application MVC PHP pour un mini marche en ligne avec authentification avancee, televersement d'images, panier a un seul produit, calcul des taxes du Quebec et paiement Stripe Checkout.

## Mise en route

1. Deployer ou copier le projet dans un dossier servi par Apache avec `mod_rewrite` active.
2. Verifier [config/config.php](./config/config.php:1) pour :
   - `app.base_url`
   - les acces a la base de donnees
   - les cles Stripe
3. Importer la migration [database/migrations/001_initial.sql](./database/migrations/001_initial.sql:1) dans la base `u6269176_tp2`.
4. S'assurer que `uploads/products`, `storage/logs` et `storage/mail` sont accessibles en ecriture par PHP.
5. Pointer Apache vers [index.php](./index.php:1) avec les beaux URLs actives via `.htaccess`.

## Comptes de demonstration

- `acheteur@example.com` / `Acheteur123!`
- `vendeur@example.com` / `Vendeur123!`
- `vendeuse@example.com` / `Vendeur123!`
- `membre@example.com` / `Membre123!`

Le dernier compte est cree inactif pour faciliter la demonstration de l'activation par courriel.

## Courriel en local

Si `mail()` ne fonctionne pas en local, les courriels sont aussi copies dans `storage/mail/`.

Pour tester l'activation :
- creer un compte
- ouvrir le dernier fichier genere dans `storage/mail/`
- visiter le lien d'activation contenu dans le courriel

Pour tester la reinitialisation du mot de passe :
- utiliser le formulaire "Mot de passe oublie"
- ouvrir le dernier fichier genere dans `storage/mail/`
- visiter le lien de reinitialisation contenu dans le courriel

## Paiement Stripe

Le flux utilise Stripe Checkout :
- le formulaire `/commande` collecte les informations personnelles, de facturation et de livraison
- la carte est saisie sur la page Stripe
- au retour, l'application verifie le statut reel de la session Stripe avant de marquer la commande payee

## Tests

- Tests unitaires : [tests/run.php](./tests/run.php:1)
- Gherkin : [tests/acceptance/achat_reussi.feature](./tests/acceptance/achat_reussi.feature:1)
- Scenario PHP : [tests/acceptance/achat_reussi.php](./tests/acceptance/achat_reussi.php:1)

Le depot inclut 5 tests unitaires, dont un test avec simulacre manuel dans `AuthServiceTest`.

## Auto-evaluation

### Fonctionnalites

| Element | Points | Auto-evaluation | Note |
| --- | ---: | --- | ---: |
| Authentification de base (1.1, 1.2 et 2.1) | 10 | Inscription, connexion et deconnexion fonctionnelles | 10 |
| Authentification avancee (1.3, 1.4 et 5.8) | 10 | Activation, mot de passe oublie, se souvenir de moi, envoi de courriels | 10 |
| Modification du mot de passe (2.2) | 5 | Fonctionnelle avec validation PHP | 5 |
| CRUD produits (1.5, 4.1, 4.2 et 5.2) | 15 | Ajout, modification, suppression des produits non vendus, image, frais de service | 15 |
| Panier et achat (3.1, 3.2 et 5.1) | 10 | Panier a un seul produit, taxes du Quebec, informations personnelles/facturation/livraison | 10 |
| Paiement Stripe (5.7) | 5 | Paiement Stripe Checkout fonctionnel en mode test | 5 |
| Historiques (3.3 et 4.3) | 5 | Historique des achats et des ventes present | 5 |
| Journaux (5.3) | 5 | Journal des ventes present en base | 5 |

Sous-total fonctionnalites : **70 / 70**

### Code

| Element | Maximum | Auto-evaluation | Note |
| --- | ---: | --- | ---: |
| MVC avance | 30 | Architecture MVC separee correctement, mais il reste quelques simplifications maison | 24 |
| Validations | 10 | Validations cote PHP sur les formulaires principaux | 10 |
| Securite, XSS | 10 | Sortie echappee et usage de `innerText`, mais je reste prudent sur un oubli possible | 8 |
| Securite, CSRF | 10 | Jetons CSRF sur les formulaires POST selon la technique demandee | 10 |
| Securite, autres | 20 | HTTPS, cookies de session, ACL, CSP, upload valide, requetes preparees, mais je reste conservateur | 12 |
| Normes de programmation | 5 | Code globalement uniforme, mais quelques textes/encodages pourraient etre plus propres | 3 |
| Apparence (ergonomie, reactivite et accessibilite) | 10 | Interface fonctionnelle et responsive, mais visuellement simple et perfectible | 7 |
| Tests (5.5) | 10 | Exigence minimale atteinte, mais couverture encore limitee | 6 |
| Redimensionnement des images (5.6) | 5 | Fait | 5 |
| Fichiers | 5 | Migration, README et depot propres | 5 |

Sous-total code : **90 / 115**

### Autre

| Element | Maximum | Auto-evaluation | Note |
| --- | ---: | --- | ---: |
| Video | 10 | A fournir separement | 0 |
| Auto-evaluation | 5 | Presentee ici et detaillee | 5 |

Sous-total autre : **5 / 15**

## Total estime

- Fonctionnalites : **70 / 70**
- Code : **90 / 115**
- Autre : **5 / 15**
- Total estime actuel : **165 / 200**

Si la video est fournie correctement, le total estime devient **175 / 200**.

## Notes

- Le site est deployee sur cPanel et testable en ligne.
- Le paiement Stripe a ete verifie en mode test.
- L'interface est en francais, mais quelques accents/termes restent a polir pour maximiser la presentation.
