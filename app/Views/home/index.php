<section class="hero">
    <div>
        <p class="eyebrow">Marche local</p>
        <h1>Un espace simple pour vendre et acheter entre membres.</h1>
        <p class="hero-copy">Le site permet l'inscription, l'activation par courriel, la mise en vente de produits, l'achat avec taxes du Quebec et l'historique des transactions.</p>
        <div class="hero-actions">
            <?php if ($auth === null): ?>
                <a class="button button-primary" href="<?= e(url('/inscription')) ?>">Creer mon compte</a>
                <a class="button button-secondary" href="<?= e(url('/connexion')) ?>">Me connecter</a>
            <?php else: ?>
                <a class="button button-primary" href="<?= e(url('/panier')) ?>">Voir mon panier</a>
                <a class="button button-secondary" href="<?= e(url('/vendre')) ?>">Gerer mes ventes</a>
            <?php endif; ?>
        </div>
    </div>
    <aside class="hero-panel">
        <h2>Fonctionnalites</h2>
        <ul class="feature-list">
            <li>Activation de compte par courriel</li>
            <li>Reinitialisation du mot de passe</li>
            <li>Se souvenir de moi</li>
            <li>Panier a un seul produit</li>
            <li>Historique d'achats et de ventes</li>
        </ul>
    </aside>
</section>

<?php if ($auth === null): ?>
    <section class="panel simple-panel">
        <h2>Connexion requise pour consulter les articles</h2>
        <p>Les produits en vente sont visibles seulement une fois connecte. Creez votre compte ou ouvrez votre session pour acceder aux annonces.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="<?= e(url('/connexion')) ?>">Voir les articles</a>
            <a class="button button-secondary" href="<?= e(url('/inscription')) ?>">M'inscrire</a>
        </div>
    </section>
<?php else: ?>
    <section class="section-header">
        <div>
            <p class="eyebrow">Produits disponibles</p>
            <h2>Articles en vente</h2>
        </div>
        <p class="muted"><?= count($products) ?> produit<?= count($products) > 1 ? 's' : '' ?> disponible<?= count($products) > 1 ? 's' : '' ?></p>
    </section>

    <?php if ($products === []): ?>
        <section class="empty-state">
            <h3>Aucun produit pour le moment</h3>
            <p>Ajoutez votre premier article pour lancer le marche.</p>
        </section>
    <?php else: ?>
        <section class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <img src="<?= e(url($product->imagePath)) ?>" alt="<?= e($product->name) ?>" class="product-image">
                    <div class="product-card-body">
                        <div class="product-card-topline">
                            <span class="chip">Vendeur: <?= e($product->sellerEmail ?? 'Membre') ?></span>
                            <span class="price"><?= e($pricing->formatCents($product->priceCents)) ?></span>
                        </div>
                        <h3><?= e($product->name) ?></h3>
                        <p><?= e(mb_strimwidth($product->description, 0, 160, '...')) ?></p>
                        <dl class="price-breakdown">
                            <div>
                                <dt>Frais de service</dt>
                                <dd><?= e($pricing->formatCents($product->serviceFeeCents)) ?></dd>
                            </div>
                            <?php $summary = $pricing->summary($product->priceCents); ?>
                            <div>
                                <dt>Total estime</dt>
                                <dd><?= e($pricing->formatCents($summary['total_cents'])) ?></dd>
                            </div>
                        </dl>
                        <div class="card-actions">
                            <a class="button button-secondary" href="<?= e(url('/produits/' . $product->id)) ?>">Voir le detail</a>
                            <?php if ($auth->id !== $product->sellerUserId): ?>
                                <form method="post" action="<?= e(url('/panier/ajouter/' . $product->id)) ?>">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token('cart-add-' . $product->id)) ?>">
                                    <button class="button button-primary" type="submit">Ajouter au panier</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
<?php endif; ?>
