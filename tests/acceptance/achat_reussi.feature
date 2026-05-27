Fonctionnalité: Achat d'un produit
  Afin d'acheter un produit disponible
  En tant qu'acheteur connecté
  Je veux pouvoir payer avec Stripe et voir mon achat dans mon historique

  Scénario: Acheter un produit disponible
    Étant donné qu'un acheteur possède un compte activé
    Et qu'un vendeur a publié un produit disponible
    Quand l'acheteur se connecte
    Et qu'il ajoute le produit au panier
    Et qu'il remplit son formulaire de commande
    Et qu'il complète le paiement sur Stripe
    Alors le produit devient vendu
    Et la commande apparaît dans l'historique d'achats
    Et la vente apparaît dans l'historique des ventes
    Et un journal de vente est enregistré
