<section class="section-header">
    <div>
        <p class="eyebrow">Panier</p>
        <h1>Votre sélection</h1>
    </div>
</section>

<?php if ($product === null || !$product->isAvailable()): ?>
    <section class="empty-state">
        <h2>Votre panier est vide</h2>
        <p>Ajoutez un produit pour commencer.</p>
        <a class="button button-primary" href="<?= e(url('/')) ?>">Voir les produits</a>
    </section>
<?php else: ?>
    <?php $summary = $pricing->summary($product->priceCents); ?>
    <section class="cart-grid">
        <article class="product-card">
            <img src="<?= e(url($product->imagePath)) ?>" alt="<?= e($product->name) ?>" class="product-image">
            <div class="product-card-body">
                <h2><?= e($product->name) ?></h2>
                <p><?= e($product->description) ?></p>
            </div>
        </article>
        <aside class="panel">
            <h2>Résumé du panier</h2>
            <dl class="summary-table">
                <div><dt>Sous-total</dt><dd><?= e($pricing->formatCents($summary['subtotal_cents'])) ?></dd></div>
                <div><dt>Frais de service</dt><dd><?= e($pricing->formatCents($summary['service_fee_cents'])) ?></dd></div>
                <div><dt>TPS</dt><dd><?= e($pricing->formatCents($summary['gst_cents'])) ?></dd></div>
                <div><dt>TVQ</dt><dd><?= e($pricing->formatCents($summary['qst_cents'])) ?></dd></div>
                <div class="summary-total"><dt>Total</dt><dd><?= e($pricing->formatCents($summary['total_cents'])) ?></dd></div>
            </dl>
            <div class="stack-actions">
                <a class="button button-primary" href="<?= e(url('/commande')) ?>">Procéder au paiement</a>
                <form method="post" action="<?= e(url('/panier/vider')) ?>">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token('cart-clear')) ?>">
                    <button class="button button-secondary" type="submit">Vider le panier</button>
                </form>
            </div>
        </aside>
    </section>
<?php endif; ?>
