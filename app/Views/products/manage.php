<section class="section-header">
    <div>
        <p class="eyebrow">Vendre</p>
        <h1>Mes articles</h1>
    </div>
    <a class="button button-primary" href="<?= e(url('/produits/ajouter')) ?>">Ajouter un produit</a>
</section>

<section class="panel wide-panel">
    <?php if ($products === []): ?>
        <p>Vous n'avez encore aucun article. Ajoutez votre premier produit pour commencer à vendre.</p>
    <?php else: ?>
        <div class="dashboard-products">
            <?php foreach ($products as $product): ?>
                <div class="dashboard-product-row">
                    <div class="dashboard-product-main">
                        <img src="<?= e(url($product->imagePath)) ?>" alt="<?= e($product->name) ?>" class="dashboard-thumb">
                        <div>
                            <strong><?= e($product->name) ?></strong>
                            <p class="muted"><?= e($product->isAvailable() ? 'Disponible' : 'Vendu') ?> - <?= e($pricing->formatCents($product->priceCents)) ?></p>
                        </div>
                    </div>
                    <?php if ($product->isAvailable()): ?>
                        <div class="inline-actions">
                            <a class="button button-secondary" href="<?= e(url('/produits/' . $product->id . '/modifier')) ?>">Modifier</a>
                            <form method="post" action="<?= e(url('/produits/' . $product->id . '/supprimer')) ?>">
                                <input type="hidden" name="_csrf" value="<?= e(csrf_token('product-delete-' . $product->id)) ?>">
                                <button class="button button-danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <span class="muted">Article déjà vendu</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
