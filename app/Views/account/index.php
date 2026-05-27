<section class="section-header">
    <div>
        <p class="eyebrow">Tableau de bord</p>
        <h1>Bonjour <?= e($user->email) ?></h1>
    </div>
    <a class="button button-primary" href="<?= e(url('/produits/ajouter')) ?>">Ajouter un produit</a>
</section>

<section class="dashboard-grid">
    <article class="panel">
        <h2>Mon compte</h2>
        <p>Courriel: <?= e($user->email) ?></p>
        <div class="stack-actions">
            <a class="button button-secondary" href="<?= e(url('/compte/mot-de-passe')) ?>">Modifier mon mot de passe</a>
            <a class="button button-secondary" href="<?= e(url('/compte/achats')) ?>">Voir mes achats</a>
            <a class="button button-secondary" href="<?= e(url('/compte/ventes')) ?>">Voir mes ventes</a>
        </div>
    </article>

    <article class="panel">
        <h2>Mes produits</h2>
        <?php if ($products === []): ?>
            <p>Vous n’avez aucun produit en vente pour l’instant.</p>
        <?php else: ?>
            <div class="dashboard-products">
                <?php foreach ($products as $product): ?>
                    <div class="dashboard-product-row">
                        <div>
                            <strong><?= e($product->name) ?></strong>
                            <p class="muted"><?= e($product->isAvailable() ? 'Disponible' : 'Vendu') ?></p>
                        </div>
                        <?php if ($product->isAvailable()): ?>
                            <div class="inline-actions">
                                <a class="button button-secondary" href="<?= e(url('/produits/' . $product->id . '/modifier')) ?>">Modifier</a>
                                <form method="post" action="<?= e(url('/produits/' . $product->id . '/supprimer')) ?>">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token('product-delete-' . $product->id)) ?>">
                                    <button class="button button-danger" type="submit">Supprimer</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>
