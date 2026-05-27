<section class="product-detail">
    <img src="<?= e(url($product->imagePath)) ?>" alt="<?= e($product->name) ?>" class="product-detail-image">
    <div class="panel">
        <p class="eyebrow">Produit</p>
        <h1><?= e($product->name) ?></h1>
        <p class="product-description"><?= e($product->description) ?></p>
        <?php $summary = $pricing->summary($product->priceCents); ?>
        <dl class="summary-table">
            <div><dt>Prix</dt><dd><?= e($pricing->formatCents($product->priceCents)) ?></dd></div>
            <div><dt>Frais de service</dt><dd><?= e($pricing->formatCents($product->serviceFeeCents)) ?></dd></div>
            <div><dt>TPS</dt><dd><?= e($pricing->formatCents($summary['gst_cents'])) ?></dd></div>
            <div><dt>TVQ</dt><dd><?= e($pricing->formatCents($summary['qst_cents'])) ?></dd></div>
            <div class="summary-total"><dt>Total à payer</dt><dd><?= e($pricing->formatCents($summary['total_cents'])) ?></dd></div>
        </dl>
        <div class="card-actions">
            <?php if ($auth === null): ?>
                <a class="button button-primary" href="<?= e(url('/connexion')) ?>">Se connecter pour acheter</a>
            <?php elseif ($auth->id === $product->sellerUserId && $product->isAvailable()): ?>
                <a class="button button-primary" href="<?= e(url('/produits/' . $product->id . '/modifier')) ?>">Modifier l'annonce</a>
            <?php elseif ($product->isAvailable()): ?>
                <form method="post" action="<?= e(url('/panier/ajouter/' . $product->id)) ?>">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token('cart-add-' . $product->id)) ?>">
                    <button class="button button-primary" type="submit">Ajouter au panier</button>
                </form>
            <?php endif; ?>
            <a class="button button-secondary" href="<?= e(url('/')) ?>">Retour aux produits</a>
        </div>
    </div>
</section>
