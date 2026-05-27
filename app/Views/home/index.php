<section class="hero">
    <div>
        <p class="eyebrow">Marche local</p>
        <h1>Articles en vente</h1>
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
</section>

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
                    <div class="card-actions">
                        <a class="button button-secondary" href="<?= e(url('/produits/' . $product->id)) ?>">Voir le detail</a>
                        <?php if ($auth === null): ?>
                            <a class="button button-primary" href="<?= e(url('/connexion')) ?>">Se connecter pour acheter</a>
                        <?php elseif ($auth->id === $product->sellerUserId): ?>
                            <a class="button button-primary" href="<?= e(url('/produits/' . $product->id . '/modifier')) ?>">Modifier l'annonce</a>
                        <?php else: ?>
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
